<?php
require 'Header.php';

if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in']) {
    // Redirection vers le tableau de bord si l'utilisateur est déjà connecté
    echo "<script>window.location.href='espace-admin-secret/espaceAdminSecret.php';</script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_SESSION['login_attempts'] >= 5) {
        $error_message = "Trop de tentatives. Veuillez réessayer plus tard.";
    } else {
        $password = $_POST['password'] ?? '';
        $passwordAdmin = getenv('PASSWORD_ADMIN') ?: $_ENV["PASSWORD_ADMIN"];
        if ($password === $passwordAdmin) {
            session_regenerate_id(true); // prévient la fixation
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['login_attempts'] = 0; // reset compteur
            echo "<script>window.location.href='espace-admin-secret/espaceAdminSecret.php';</script>";
            exit;
        } else {
            $_SESSION['login_attempts']++;
            $error_message = "Mot de passe incorrect. Tentative {$_SESSION['login_attempts']} sur 5.";
        }
    }
}
?>

<main>
    <h1>Connexion à l'espace administrateur</h1>

    <?php if (!empty($error_message)) : ?>
        <p style="color:red;"><?php echo htmlspecialchars($error_message); ?></p>
    <?php endif; ?>

    <?php if ($_SESSION['login_attempts'] < 5): ?>
    <form action="admin" method="post">
        <div>
            <label for="password">Mot de passe :</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit">Se connecter</button>
    </form>
    <?php else: ?>
        <p>Veuillez patienter avant de réessayer.</p>
    <?php endif; ?>
</main>

<?php require 'Footer.php'; ?>