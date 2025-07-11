<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();  // Démarre la session seulement si elle n'est pas déjà active
}

define('BASE_URL', 'http://localhost:8085/');

setlocale(LC_TIME, 'fr_FR.UTF-8', 'fr_FR', 'fr');
date_default_timezone_set('Europe/Paris');

$canonical_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http");
$canonical_url .= "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$canonical_url = strtok($canonical_url, '?');
$canonical_url = htmlspecialchars($canonical_url);

$titleIndex = "Page d'accueil - Portfolio développeur polyvalent | EliDev";
$pageTitle = isset($pageTitle) ? htmlspecialchars($pageTitle) : htmlspecialchars($titleIndex);

$descriptionIndex = "Portfolio de développeur polyvalent, spécialisé dans la création d'outils en ligne pour l'édition d'images, la conversion de fichiers et le téléchargement de vidéos.";
$pageDescription = isset($pageDescription) ? htmlspecialchars($pageDescription) : htmlspecialchars($descriptionIndex);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title><?= $pageTitle ?></title>
    <meta name="description" content="<?= $pageDescription ?>">

    <meta property="og:url" content="<?= $canonical_url ?>" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="<?= $pageTitle ?>" />
    <meta property="og:description" content="<?= $pageDescription ?>" />
    <meta property="og:image" content="<?= BASE_URL ?>images/Logo_EliDev.webp" />

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:image" content="<?= BASE_URL ?>images/Logo_EliDev.webp" />
    <meta name="twitter:image:alt" content="Image">
    <meta name="twitter:title" content="<?= $pageTitle ?>">
    <meta name="twitter:description" content="<?= $pageDescription ?>">
    <meta name="twitter:site" content="@EliDev">
    <meta name="twitter:creator" content="@EliDev">

    <meta name="robots" content="index, follow">
    <meta name="revisit-after" content="7 days">
    <meta name="language" content="fr">

    <meta name="author" content="EliDev">
    <meta name="publisher" content="EliDev">
    <meta name="copyright" content="&copy; <?= date('Y') ?> EliDev">

    <!-- Préconnexion pour accélérer la résolution DNS des domaines critiques -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- Précharger la feuille de style critique (police Roboto) -->
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700;900&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript>
      <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700;900&display=swap">
    </noscript>

    <meta name="keywords" content="portfolio, développeur, polyvalent, développeur polyvalent, full stack, web developer, JS, JavaScript, PHP, HTML, CSS, Java, Python, Excel, VBA, SQL, Access, Symfony, Docker, Git, GitHub, Eliot Dubreuil, Eliot, Dubreuil, Eli, Dev, EliDev">

    <link rel="canonical" href="<?= $canonical_url ?>">
    <link rel="icon" href="<?= BASE_URL ?>images/Logo_EliDev.webp" type="image/x-icon">
    <link rel="stylesheet" href="<?= BASE_URL ?>styles/global.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    

    <!-- Schema.org -->
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "WebSite",
            "name": "Portfolio EliDev",
            "url": "<?= $canonical_url ?>",
            "description": "<?= $pageDescription ?>",
            "image": "<?= BASE_URL ?>images/Logo_EliDev.webp"
        }
    </script>
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><a href="<?= BASE_URL ?>">Accueil</a></li>
                <li><a href="<?= BASE_URL ?>">Projets</a></li>
                <li><a href="<?= BASE_URL ?>">Contact</a></li>
            </ul>
        </nav>
    </header>

    <?php
    // Include any additional scripts or styles here
    ?>