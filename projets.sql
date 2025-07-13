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
('Projet 1', 'Description du projet 1', '2023-01-01', 'Web', 'http://example.com/projet1', '/images/projet1.jpg', 'Contenu détaillé du projet 1'),
('Projet 2', 'Description du projet 2', '2023-02-01', 'Mobile', 'http://example.com/projet2', '/images/projet2.jpg', 'Contenu détaillé du projet 2'),
('Projet 3', 'Description du projet 3', '2023-03-01', 'Desktop', 'http://example.com/projet3', '/images/projet3.jpg', 'Contenu détaillé du projet 3');


-- Créer la table Competences
CREATE TABLE Competences (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    logoPath VARCHAR(255) NOT NULL,
    type VARCHAR(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insérer des données dans la table Competences
INSERT INTO Competences (nom, logoPath, type) VALUES
('PHP', '/images-competences/php.png', 'Langage'),
('JavaScript', '/images-competences/javascript.png', 'Langage'),
('Swift', '/images-competences/swift.png', 'Langage'),
('Dart', '/images-competences/dart.png', 'Langage'),
('HTML/CSS', '/images-competences/html-css.png', 'Langage'),
('Python', '/images-competences/python.png', 'Langage'),
('Java', '/images-competences/java.png', 'Langage'),
('C', '/images-competences/csharp.png', 'Langage'),
('MySQL', '/images-competences/mysql.png', 'BBD'),
('Access', '/images-competences/access.png', 'BBD'),
('Git', '/images-competences/git.png', 'Outil'),
('Docker', '/images-competences/docker.png', 'Outil'),
('Windows', '/images-competences/windows.png', 'Outil'),
('MacOS', '/images-competences/macos.png', 'Outil'),
('Linux', '/images-competences/linux.png', 'Outil'),
('Figma', '/images-competences/figma.png', 'Outil'),
('Photoshop', '/images-competences/photoshop.png', 'Outil'),
('Symfony', '/images-competences/symfony.png', 'Framework'),
('Spring Boot', '/images-competences/spring-boot.png', 'Framework'),
('Flutter', '/images-competences/flutter.png', 'Framework');