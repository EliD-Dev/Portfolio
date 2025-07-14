<?php
header('Content-Type: text/plain'); // Indique que la réponse est en texte brut

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo "Accès interdit";
    exit;
}

if (!empty($_POST['website'])) {
    echo "Bot détecté.";
    exit;
}

if (session_status() == PHP_SESSION_NONE) {
    session_set_cookie_params([
        'secure' => true,   // nécessite HTTPS
        'httponly' => true,
        'samesite' => 'Strict'
    ]);
    session_start();  // Démarre la session seulement si elle n'est pas déjà active
}
if (!isset($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    echo "Token CSRF invalide";
    exit;
}
unset($_SESSION['csrf_token']);

$now = time();
if (isset($_SESSION['last_contact']) && $now - $_SESSION['last_contact'] < 30) {
    echo "Trop de requêtes. Veuillez patienter avant de réessayer.";
    exit;
}
$_SESSION['last_contact'] = $now;

// Récupération + validation des champs
$name    = htmlspecialchars(trim($_POST["name"]    ?? ""));
$subject = htmlspecialchars(trim($_POST["subject"] ?? ""));
$email   = htmlspecialchars(trim($_POST["email"]   ?? ""));
$message = htmlspecialchars(trim($_POST["message"] ?? ""));
$message = strip_tags($message); // Supprimer les balises HTML
$attach  = $_FILES["pièce_jointe"] ?? null;

if (!$name || !$subject || !$email || !$message) {
    echo "Tous les champs sont requis.";
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "Adresse email invalide.";
    exit;
}

if (preg_match('/[\r\n]/', $name.$subject.$email)) {
    echo "Tentative d'injection détectée.";
    exit;
}

function scanWithVirustotal($filePath, $apiKey) {
    $cfile = curl_file_create($filePath);

    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => "https://www.virustotal.com/api/v3/files",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            "x-apikey: $apiKey"
        ],
        CURLOPT_POSTFIELDS => ['file' => $cfile]
    ]);
    $response = curl_exec($curl);
    $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);
    if ($http_code !== 200) {
        return ["success" => false, "error" => "Erreur d'envoi à VirusTotal"];
    }
    $data = json_decode($response, true);
    if (!isset($data["data"]["id"])) {
        return ["success" => false, "error" => "Réponse mal formée"];
    }
    return [
        "success" => true,
        "scan_id" => $data["data"]["id"]
    ];
}

// Gestion éventuelle de l’upload
$uploadFile = null;
if ($attach && $attach["error"] === UPLOAD_ERR_OK) {
    $uploadDir = __DIR__ . '/uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    $ext = pathinfo($attach["name"], PATHINFO_EXTENSION);
    $randomName = bin2hex(random_bytes(16)) . ($ext ? ".$ext" : "");
    $uploadFile = $uploadDir . $randomName;
    if (!move_uploaded_file($attach["tmp_name"], $uploadFile)) {
        echo "Erreur lors du téléchargement du fichier.";
        exit;
    }
    $apiKey = "26b77f5daa29cdf348ef673839bf150d396bc0c76a381a63d7f4de477142ed2c";
    $scanResult = scanWithVirustotal($uploadFile, $apiKey);
    if (!$scanResult["success"]) {
        unlink($uploadFile); // Supprime le fichier potentiellement dangereux
        echo "Fichier rejeté : " . $scanResult["error"];
        exit;
    }
}

// Paramètres SMTP Gmail
$smtp_host     = 'smtp.gmail.com';
$smtp_port     = 587;
$smtp_user     = getenv('GOOGLE_EMAIL') ?: $_ENV["GOOGLE_EMAIL"];
$smtp_password = getenv('GOOGLE_PASSWORD_APP') ?: $_ENV["GOOGLE_PASSWORD_APP"];

// Construction du message MIME (texte + éventuelle pièce jointe)
$boundary = md5(uniqid());
$headers  = [];
$headers[] = "From: {$email}";
$headers[] = "Reply-To: {$email}";
$headers[] = "MIME-Version: 1.0";
$headers[] = "Content-Type: multipart/mixed; boundary=\"{$boundary}\"";
$body  = "--{$boundary}\r\n";
$body .= "Content-Type: text/plain; charset=UTF-8\r\n";
$body .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
$body .= "Nom    : {$name}\n";
$body .= "Email  : {$email}\n";
$body .= "Message: {$message}\n\n";

if ($uploadFile) {
    $fileContent = chunk_split(base64_encode(file_get_contents($uploadFile)));
    $filename    = basename($uploadFile);
    $mimeType    = mime_content_type($uploadFile);
    $body .= "--{$boundary}\r\n";
    $body .= "Content-Type: {$mimeType}; name=\"{$filename}\"\r\n";
    $body .= "Content-Transfer-Encoding: base64\r\n";
    $body .= "Content-Disposition: attachment; filename=\"{$filename}\"\r\n\r\n";
    $body .= $fileContent . "\r\n\r\n";
}

// Fin de message
$body .= "--{$boundary}--\r\n";

// Fonction d’envoi SMTP basique
function smtpSend($host, $port, $user, $pass, $subject, $headers, $body) {
    $to = 'eliotdubreuil@gmail.com';
    $fp = stream_socket_client("tcp://{$host}:{$port}", $errno, $errstr, 10);
    if (!$fp) {
        return "Connexion SMTP échouée : $errstr ($errno)";
    }
    stream_set_timeout($fp, 5);
    $read = fn() => fgets($fp, 515);
    // Attente du 220
    if (substr($read(), 0, 3) !== '220') {
        return "Erreur initiale SMTP";
    }
    // EHLO + lecture complète
    fwrite($fp, "EHLO localhost\r\n");
    do {
        $line = $read();
    } while (isset($line[3]) && $line[3] === '-');
    // Passage en TLS
    fwrite($fp, "STARTTLS\r\n");
    if (substr($read(), 0, 3) !== '220') {
        return "STARTTLS refusé";
    }
    stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT);
    // Relance EHLO après STARTTLS
    fwrite($fp, "EHLO localhost\r\n");
    do {
        $line = $read();
    } while (isset($line[3]) && $line[3] === '-');
    // AUTH LOGIN
    fwrite($fp, "AUTH LOGIN\r\n");
    $read();
    fwrite($fp, base64_encode($user) . "\r\n");
    $read();
    fwrite($fp, base64_encode($pass) . "\r\n");
    if (substr($read(), 0, 3) !== '235') {
        return "Authentification SMTP échouée";
    }
    // MAIL FROM
    fwrite($fp, "MAIL FROM:<{$user}>\r\n");
    $read();
    // RCPT TO
    fwrite($fp, "RCPT TO:<{$to}>\r\n");
    $read();
    // DATA
    fwrite($fp, "DATA\r\n");
    $read();
    // Envoi des headers + body
    $full  = "Subject: {$subject}\r\n" . implode("\r\n", $headers) . "\r\n\r\n" . $body . "\r\n.\r\n";
    fwrite($fp, $full);
    if (substr($read(), 0, 3) !== '250') {
        return "Envoi DATA échoué";
    }
    // QUIT
    fwrite($fp, "QUIT\r\n");
    fclose($fp);
    return true;
}

// Envoi
$result = smtpSend(
    $smtp_host, $smtp_port,
    $smtp_user, $smtp_password,
    "=?UTF-8?B?" . base64_encode("Nouveau message de contact : {$subject}") . "?=",
    $headers,
    $body
);

if ($result === true) {
    echo "Message envoyé avec succès.";
} else {
    echo "Erreur lors de l'envoi du message : $result";
}

if ($uploadFile) {
    unlink($uploadFile);
}