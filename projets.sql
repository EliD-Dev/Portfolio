-- Créer la base de données
CREATE DATABASE Portfolio CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

-- Utiliser la base de données
USE Portfolio;

-- Créer la table Projets
CREATE TABLE Projets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(255) NOT NULL,
    description VARCHAR(500),
    imagePath VARCHAR(255),
    contenu LONGTEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insérer des données dans la table Projets
INSERT INTO Projets (titre, description, imagePath, contenu) VALUES
('Mon premier projet', 'Description de mon premier projet', 'images-projets/image1.jpg', 'Contenu détaillé de mon premier projet'),
('Mon deuxième projet', 'Description de mon deuxième projet', 'images-projets/image2.jpg', 'Contenu détaillé de mon deuxième projet'),
('Mon troisième projet', 'Description de mon troisième projet', 'images-projets/image3.jpg', 'Contenu détaillé de mon troisième projet');