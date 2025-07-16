-- Supprimer la base de données si elle existe déjà
DROP DATABASE IF EXISTS Portfolio;

-- Créer la base de données
CREATE DATABASE Portfolio CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;


-- Utiliser la base de données
USE Portfolio;

-- Supprimer les tables si elles existent déjà
DROP TABLE IF EXISTS Projets;
DROP TABLE IF EXISTS Competences;

-- Créer la table Projets
CREATE TABLE Projets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(255) NOT NULL,
    description VARCHAR(500) NOT NULL,
    date DATE DEFAULT NULL,
    type VARCHAR(50) DEFAULT NULL,
    url VARCHAR(255) DEFAULT NULL,
    imagePath VARCHAR(255) DEFAULT NULL,
    contenu LONGTEXT DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insérer des données dans la table Projets
INSERT INTO Projets (titre, description, date, type, url, imagePath, contenu) VALUES
('Site web Salon Vins et Saveurs', 'Création complète du site vitrine et de l\'espace exposants pour la première édition du Salon Vins et Saveurs organisé par le Kiwanis Saint-Dié-des-Vosges.', '2025-04-11', 'Web', 'https://vins-et-saveurs.com', '/images-projets/Logo_Salon.webp', '<h2>Contexte du projet</h2>
    <p>Ce projet a été réalisé durant mon stage de 8 semaines au sein du Kiwanis Saint-Dié-des-Vosges, du 17 février au 11 avril 2025. L\'objectif était de créer un site web complet pour promouvoir et gérer le <strong>Salon Vins et Saveurs</strong>, un nouvel événement destiné à financer des actions en faveur des enfants.</p>

    <h2>Fonctionnalités principales</h2>
    <ul>
        <li>Pages publiques présentant l\'événement (accueil, infos visiteurs, partenaires, contact, mentions légales).</li>
        <li>Section exposants avec liste filtrable et fiches détaillées, ainsi qu\'un formulaire « Devenir exposant ».</li>
        <li>Espace administrateur sécurisé permettant de gérer dynamiquement :
            <ul>
                <li>les exposants (CRUD complet),</li>
                <li>les mots-clés et partenaires,</li>
                <li>les dates, tarifs et programmes,</li>
                <li>les statistiques (billetterie, exposants, partenaires).</li>
            </ul>
        </li>
        <li>Intégration de la billetterie HelloAsso via API pour la réservation et le paiement en ligne.</li>
    </ul>

    <h2>Aspects techniques</h2>
    <ul>
        <li>Développement en <strong>PHP 7.2</strong> et <strong>MySQL</strong> sur un serveur <strong>Windows Server 2012</strong> avec IIS.</li>
        <li>Scripts Batch et WinSCP pour synchronisation automatique via SFTP.</li>
        <li>Optimisation du <code>web.config</code> pour les réécritures d\'URL et la prise en charge des images WebP/AVIF.</li>
    </ul>

    <h2>Résultat</h2>
    <p>Le site est en production depuis avril 2025 et géré en autonomie par les bénévoles du Kiwanis, qui actualisent directement les contenus et suivent la billetterie depuis l\'interface d\'administration.</p>'),
(
    'Jeu 2D plateforme - Marouxo',
    'Création d\'un jeu vidéo 2D en Python avec Pygame, inspiré des jeux Mario, réalisé en terminale.',
    '2023-06-01',
    'Desktop',
    'https://github.com/EliD-Dev/Marouxo_Python',
    '/images-projets/Marouxo_Logo.webp',
    '<h2>Contexte du projet</h2>
    <p>Projet réalisé au lycée en 2023 dans le cadre de notre terminale, ce jeu de type plateforme a été développé en Python avec le module Pygame. Il s\'agit d\'un hommage aux classiques du genre, notamment <strong>Super Mario Bros</strong>.</p>

    <h2>Synopsis</h2>
    <p>Notre héros, <strong>Marouxo</strong>, doit sauver son ami Gustave et Toad qui ont été enlevés par de cruels pandas n\'aimant pas les roux. Pour les retrouver, il devra traverser une jungle dangereuse peuplée de singes, serpents, plantes carnivores et pandas maléfiques.</p>

    <h2>Caractéristiques techniques</h2>
    <ul>
        <li>Développé en <strong>Python 3.7+</strong> avec <strong>Pygame 2.3.0</strong>.</li>
        <li>Animations du héros : marche, course, saut.</li>
        <li>Sprites d\'ennemis : singes, serpents, plantes carnivores (qui crachent des boules de feu), pandas.</li>
        <li>Décors vivants avec animaux passifs (chenille, crevette, crabe), plateformes en branches et blocs de terre.</li>
    </ul>

    <h2>Gameplay</h2>
    <ul>
        <li>Contrôles via les flèches directionnelles pour se déplacer et sauter.</li>
        <li>Le joueur peut sauter sur les ennemis pour les éliminer.</li>
        <li>Les plantes carnivores restent fixes et tirent des projectiles, les autres ennemis patrouillent de gauche à droite.</li>
        <li>Démarrage du jeu avec <kbd>Enter</kbd> ou un clic sur le bouton « Jouer ».</li>
    </ul>

    <h2>Objectifs pédagogiques</h2>
    <p>Ce projet nous a permis de renforcer nos compétences en programmation Python, de découvrir la création de sprites et animations, et de comprendre les mécaniques fondamentales d\'un jeu de plateforme.</p>'
),
(
    'Pokédex SwiftUI (fusion app cinéma)',
    'Développement d\'un Pokédex complet en SwiftUI intégré dans une application existante pour créer un hub multi-thèmes.',
    '2025-05-23',
    'Mobile',
    'https://github.com/Med-css/cinema',
    '/images-projets/pokeapi.webp',
    '<h2>Contexte du projet</h2>
    <p>Projet de BUT informatique de 2ème année en SwiftUI réalisé en 2025, consistant à créer un <strong>Pokédex interactif</strong> consommant l\'API officielle <a href=\"https://pokeapi.co\" target=\"_blank\">PokeAPI</a>, puis à le fusionner avec une application cinéma existante pour former un hub multi-thèmes.</p>

    <h2>Fonctionnalités principales</h2>
    <ul>
        <li>Choix depuis l\'accueil entre voir les films ou explorer le Pokédex.</li>
        <li>Chargement des Pokémon par génération (1 à 5) via PokeAPI.</li>
        <li>Affichage des noms français, types traduits, sprites, statistiques et aptitudes.</li>
        <li>Recherche temps réel sur le nom anglais ou français.</li>
        <li>Fiche détaillée avec stats, taille, poids et capacités.</li>
    </ul>

    <h2>Aspects techniques</h2>
    <ul>
        <li>Architecture <strong>SwiftUI</strong> utilisant NavigationView, NavigationLink, List, Picker et AsyncImage.</li>
        <li>Utilisation combinée de <code>URLSession</code> et <code>DispatchGroup</code> pour synchroniser les multiples appels API.</li>
        <li>Traduction automatique des types et statistiques en français avec mapping personnalisé.</li>
    </ul>

    <h2>Résultat</h2>
    <p>Cette fusion démontre la capacité à étendre et interconnecter des modules indépendants dans une seule application SwiftUI fluide et responsive.</p> '
),
(
    'WikiCoaster (Symfony)',
    'Projet de 2ᵉ année de BUT Informatique, destiné à l\'apprentissage et la mise en pratique du framework Symfony, à travers la gestion de montagnes russes et de parcs d\'attraction.',
    '2025-01-22',
    'Web',
    'https://github.com/EliD-Dev/WikiCoaster',
    '/images-projets/WikiCoaster.webp',
    '<h2>Contexte du projet</h2>
    <p>Projet réalisé dans le cadre de ma 2<sup>ème</sup> année de BUT Informatique, visant à maîtriser Symfony et ses concepts avancés. L\'objectif était de développer un site web complet en local pour gérer des fiches de montagnes russes et des parcs d\'attraction.</p>

    <h2>Fonctionnalités principales</h2>
    <ul>
        <li><strong>CRUD complet</strong> sur les coasters et les parcs via Symfony et Doctrine.</li>
        <li><strong>Interface administrateur</strong> grâce à EasyAdminBundle pour gérer facilement les données.</li>
        <li><strong>Pages publiques</strong> permettant la consultation des coasters, parcs, fabricants, pays et types.</li>
        <li><strong>Recherche et filtrage dynamique</strong> des coasters par type, pays ou constructeur.</li>
    </ul>

    <h2>Aspects techniques</h2>
    <ul>
        <li>Utilisation du framework <strong>Symfony 6.4</strong> et du moteur de templates <strong>Twig</strong>.</li>
        <li>Mapping des entités avec <strong>Doctrine ORM</strong> et gestion des migrations.</li>
        <li>Base de données relationnelle <strong>MySQL</strong>.</li>
        <li>Form validation et routing avancé via annotations.</li>
    </ul>

    <h2>Résultat</h2>
    <p>Ce projet m\'a permis de consolider mes compétences en PHP moderne, en conception MVC et en manipulation de bases relationnelles, tout en produisant un site robuste et évolutif.</p>'
);



-- Créer la table Competences
CREATE TABLE Competences (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    logoPath VARCHAR(255) NOT NULL,
    type VARCHAR(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insérer des données dans la table Competences
INSERT INTO Competences (nom, logoPath, type) VALUES
('HTML/CSS', '/images-competences/html-css.webp', 'Langage'),
('JavaScript', '/images-competences/js.webp', 'Langage'),
('PHP', '/images-competences/php.webp', 'Langage'),
('Dart', '/images-competences/dart.webp', 'Langage'),
('ASP Classic', '/images-competences/asp.webp', 'Langage'),
('Python', '/images-competences/python.webp', 'Langage'),
('Java', '/images-competences/java.webp', 'Langage'),
('C', '/images-competences/c.webp', 'Langage'),
('Swift', '/images-competences/swift.webp', 'Langage'),
('VBA (Excel)', '/images-competences/vba.webp', 'Langage'),
('MySQL', '/images-competences/mysql.webp', 'BDD'),
('Access', '/images-competences/access.webp', 'BDD'),
('Git', '/images-competences/git.webp', 'Outil'),
('VS Code', '/images-competences/vscode.webp', 'Outil'),
('Docker', '/images-competences/docker.webp', 'Outil'),
('Figma', '/images-competences/figma.webp', 'Outil'),
('Photoshop', '/images-competences/photoshop.webp', 'Outil'),
('Trello', '/images-competences/trello.webp', 'Outil'),
('Postman', '/images-competences/postman.webp', 'Outil'),
('Windows', '/images-competences/windows.webp', 'Outil'),
('MacOS', '/images-competences/macos.webp', 'Outil'),
('Linux', '/images-competences/linux.webp', 'Outil'),
('Symfony', '/images-competences/symfony.webp', 'Framework'),
('Flutter', '/images-competences/flutter.webp', 'Framework'),
('Spring Boot', '/images-competences/spring-boot.webp', 'Framework');