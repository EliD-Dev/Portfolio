<?php
if (session_status() == PHP_SESSION_NONE) {
    session_set_cookie_params([
        'secure' => true,   // nécessite HTTPS
        'httponly' => true,
        'samesite' => 'Strict'
    ]);
    session_start();  // Démarre la session seulement si elle n'est pas déjà active
}

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Génère un token CSRF sécurisé
}

if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
}

if (!isset($_SESSION['last_contact'])) {
    $_SESSION['last_contact'] = 0; // Initialise le timestamp de la dernière soumission
}

require "dbconnect.php";
$connexionDB = Database::getInstance();

define('BASE_URL', 'http://localhost:'.getenv('WEB_PORT').'/');

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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    

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
            <a href="<?= BASE_URL ?>"><img src="<?= BASE_URL ?>images/Logo_EliDev.webp" alt="Logo EliDev" class="logo"></a>
            <div>
                <a href="<?= BASE_URL ?>#projets">Projets</a>
                <a href="<?= BASE_URL ?>#competences">Compétences</a>
                <a href="<?= BASE_URL ?>#contact">Contact</a>
            </div>
        </nav>
    </header>

    <?php
        // Fonction pour générer un slug à partir d'un titre
        function slugify($text) {
            // Remplace les accents, caractères spéciaux, etc.
            $text = iconv('UTF-8', 'ASCII//TRANSLIT', $text);
            $text = preg_replace('/[^a-zA-Z0-9\/_| -]/', '', $text);
            $text = strtolower(trim($text, '-'));
            $text = preg_replace('/[\/_| -]+/', '-', $text);
            return $text;
        }
    ?>

    <div id="loader"><div class="loader-spinner"></div></div>

    <script>
        function getItemsPerPage() {
            if (window.innerWidth <= 710) {
                return 1;
            } else if (window.innerWidth <= 1000) {
                return 2;
            } else {
                return 3;
            }
        }

        window.addEventListener("load", () => {
            const loader = document.getElementById("loader");
            if (loader) {
                loader.style.display = "none";
            }

            const main = document.querySelector(".index-main");
            if (main) {
                setTimeout(() => {
                    main.classList.add("loaded");
                }, 200);
            }

            const projets = document.querySelectorAll(".projet");
            projets.forEach((projet, index) => {
                projet.classList.add("card-flip-in");
                projet.style.setProperty('--index', index % getItemsPerPage());
            });
        });
    </script>
