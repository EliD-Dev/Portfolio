<?php
require 'Header.php';

// Récupération du slug depuis l'URL
$slug = isset($_GET['slug']) ? $_GET['slug'] : '';

$requete = $connexionDB->prepare("SELECT titre, imagePath, date, type, contenu, url FROM Projets");
$requete->execute();
$projets = $requete->fetchAll(PDO::FETCH_ASSOC);
$requete->closeCursor();

// Trouver le projet correspondant au slug
$projet = null;
foreach ($projets as $p) {
    if (slugify($p['titre']) === $slug) {
        $projet = $p;
        break;
    }
}

if (!$projet) {
    echo '<p>Projet non trouvé.</p>';
    require 'Footer.php';
    exit;
}
?>

<main>
    <h1><?= htmlspecialchars($projet['titre']); ?></h1>
    <div class="ligne">
        <p><strong>Date :</strong> <?= htmlspecialchars($projet['date']); ?></p>
        <p><strong>Type :</strong> <?= htmlspecialchars($projet['type']); ?></p>
    </div>
    <?= $projet['contenu'] ?>
    <p><a href="<?= htmlspecialchars($projet['url']); ?>" target="_blank">Voir le projet en ligne</a></p>
</main>

<?php require 'Footer.php'; ?>