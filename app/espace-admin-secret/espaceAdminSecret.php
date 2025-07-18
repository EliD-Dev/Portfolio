<?php
require '../Header.php';

if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in'] || !isset($_SESSION['login_attempts']) || $_SESSION['login_attempts'] >= 5) {
    // Redirection vers la page de connexion si l'utilisateur n'est pas connecté
    echo "<script>window.location.href='../admin';</script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    echo "<script>window.location.href='../admin';</script>";
    exit;
}
?>

<main>
    <h1>Bienvenue dans l'espace administrateur</h1>
    <h2>Gestion de mon Portfolio</h2>

    <div class="admin-dashboard">
        <h3>CRUD</h3>
        <p>Vous pouvez créer, lire, mettre à jour et supprimer les éléments suivants :</p>
        <div class="crud-buttons">
            <button onclick="window.location.href='projetsCRUD';">Projets</button>
            <button onclick="window.location.href='competencesCRUD';">Compétences</button>
        </div>
    </div>

    <!-- Ajoutez ici un bouton ou un lien pour déconnexion -->
    <form action="espaceAdminSecret" method="post">
        <button type="submit" name="logout" class="logout-button">Se déconnecter</button>
    </form>
</main>

<?php require '../Footer.php'; ?>