<?php
ob_start();
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
            if (session_status() === PHP_SESSION_ACTIVE) {
                session_regenerate_id(true);
            }
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
ob_end_flush();
?>

<main>
    <h1>Connexion à l'espace administrateur</h1>

    <?php if ($_SESSION['login_attempts'] < 5): ?>
    <form action="admin" method="post" class="admin-login-form">
        <?php if (!empty($error_message)) : ?>
            <div id="form-message" class="form-message error afficher"><?= htmlspecialchars($error_message); ?></div>
        <?php endif; ?>
        <div class="form-group-admin">
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