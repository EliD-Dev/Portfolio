<?php
require '../Header.php';

if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    // Redirection vers la page de connexion si l'utilisateur n'est pas connecté
    echo "<script>window.location.href='../admin';</script>";
    exit;
}

/*
CREATE TABLE Competences (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    logoPath VARCHAR(255) NOT NULL,
    type VARCHAR(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'] ?? null;
    $type = $_POST['type'] ?? null;

    $logoPath = null;

    if (isset($_FILES['logoPath']) && $_FILES['logoPath']['error'] === UPLOAD_ERR_OK) {
        $targetDir = '../images-competences/';
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $originalName = basename($_FILES['logoPath']['name']);
        $originalName = str_replace(' ', '_', $originalName);
        $originalPath = $targetDir . $originalName;

        // Déplacer le fichier uploadé
        if (move_uploaded_file($_FILES['logoPath']['tmp_name'], $originalPath)) {
            // Vérifier l'extension
            $extension = strtolower(pathinfo($originalPath, PATHINFO_EXTENSION));

            if ($extension !== 'webp') {
                // Déterminer le chemin du futur fichier webp
                $webpPath = $targetDir . pathinfo($originalName, PATHINFO_FILENAME) . '.webp';

                // Créer l'image selon l'extension
                switch ($extension) {
                    case 'jpg':
                    case 'jpeg':
                        $image = imagecreatefromjpeg($originalPath);
                        break;
                    case 'png':
                        $image = imagecreatefrompng($originalPath);
                        // Pour conserver la transparence
                        imagepalettetotruecolor($image);
                        imagealphablending($image, true);
                        imagesavealpha($image, true);
                        break;
                    case 'gif':
                        $image = imagecreatefromgif($originalPath);
                        break;
                    default:
                        $image = false;
                        break;
                }

                if ($image !== false) {
                    // Enregistrer en webp (qualité à 100 par exemple)
                    imagewebp($image, $webpPath, 100);
                    imagedestroy($image);

                    // Supprimer l'original si tu le souhaites
                    unlink($originalPath);

                    // Mettre à jour $logoPath pour l'URL webp
                    $logoPath = BASE_URL . 'images-competences/' . basename($webpPath);
                } else {
                    // Pas pu convertir, garder le fichier original
                    $logoPath = BASE_URL . 'images-competences/' . $originalName;
                }
            } else {
                // Déjà un webp
                $logoPath = BASE_URL . 'images-competences/' . $originalName;
            }
        } else {
            $logoPath = null;
        }
    }


    if (isset($_POST['id'])) {
        // Mise à jour
        $id = $_POST['id'];
        if (isset($_POST['action']) && $_POST['action'] === 'delete') {
            $requete = $connexionDB->prepare("DELETE FROM Competences WHERE id = :id");
            $requete->bindParam(':id', $id);
            $requete->execute();
            echo "<script>window.location.href='competencesCRUD';</script>";
            exit;
        } else {
            // Mise à jour de la compétence
            if (!$logoPath) {
                // Si le logo n'est pas mis à jour, on récupère l'ancien logo
                $requete = $connexionDB->prepare("SELECT logoPath FROM Competences WHERE id = :id");
                $requete->bindParam(':id', $id);
                $requete->execute();
                $competence = $requete->fetch(PDO::FETCH_ASSOC);
                $logoPath = $competence['logoPath'];
            }

            $requete = $connexionDB->prepare("UPDATE Competences SET nom = :nom, logoPath = :logoPath, type = :type WHERE id = :id");
            $requete->bindParam(':nom', $nom);
            $requete->bindParam(':logoPath', $logoPath);
            $requete->bindParam(':type', $type);
            $requete->bindParam(':id', $id);
            $requete->execute();
            echo "Compétence mise à jour avec succès.";
        }
    } else {
        // Ajout
        $requete = $connexionDB->prepare("INSERT INTO Competences (nom, logoPath, type) VALUES (:nom, :logoPath, :type)");
        $requete->bindParam(':nom', $nom);
        $requete->bindParam(':logoPath', $logoPath);
        $requete->bindParam(':type', $type);
        $requete->execute();
        echo "Nouvelle compétence ajoutée avec succès.";
    }
    exit;
}
?>

<main>
    <button class="admin-back-button" onclick="window.location.href='espaceAdminSecret';">Retour à l'espace administrateur</button>
    <h1>Gestion des compétences</h1>

    <div class="bouton-ajouter-mobile">
        <button onclick="ajouterCompetence();"><i class="fas fa-plus"></i> Ajouter une compétence</button>
    </div>

    <table>
        <?php
        $requete = $connexionDB->prepare("SELECT * FROM Competences");
        $requete->execute();
        $competences = $requete->fetchAll(PDO::FETCH_ASSOC);
        $requete->closeCursor();
        ?>
        <thead>
            <tr>
                <th>Nom</th>
                <th>Logo</th>
                <th>Type</th>
                <th><button onclick="ajouterCompetence();"><i class="fas fa-plus"></i></button></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($competences as $competence): ?>
                <tr>
                    <td data-label="Nom"><?= htmlspecialchars($competence['nom']); ?></td>
                    <td data-label="Logo"><button onclick="previewLogo('<?= htmlspecialchars($competence['logoPath']); ?>');"><i class="fas fa-image"></i></button></td>
                    <td data-label="Type"><?= htmlspecialchars($competence['type']); ?></td>
                    <td data-label="Actions">
                        <button onclick='editCompetence(<?= json_encode($competence, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>);'>
                            <i class="fas fa-edit"></i>
                        </button>
                        <form action="competencesCRUD" method="post" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette compétence ?');" style="display:inline;">
                            <input type="hidden" name="id" value="<?= $competence['id']; ?>">
                            <input type="hidden" name="action" value="delete">
                            <button type="submit"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</main>

<div id="modal-preview" class="modal-preview">
    <div>
        <div class="modal-right"><button class="modal-button" onclick="document.getElementById('modal-preview').style.display='none';">Fermer</button></div>
        <img id="preview-image" src="" alt="Logo de la compétence">
    </div>
</div>

<div id="modal-form" class="modal-form">
    <div id="form-container" class="form-container">
        <div class="modal-right"><button class="modal-button" onclick="document.getElementById('modal-form').style.display='none';">Fermer</button></div>
        <h2 id="competence-form-titre">Ajouter une nouvelle compétence</h2>
        <form id="competence-form" method="post" action="competencesCRUD" class="admin-login-form" enctype="multipart/form-data">
            <div class="form-group">
                <label for="nom">Nom :</label>
                <input type="text" id="nom" name="nom" required>
            </div>
            <div class="form-group">
                <label for="logoPath">Logo :</label>
                <input type="file" id="logoPath" name="logoPath" accept="image/*" required>
            </div>
            <div class="form-group">
                <label for="type">Type :</label>
                <select name="type" id="type">
                    <option disabled selected value>-- Sélectionner un type --</option>
                    <option value="Langage">Langage</option>
                    <option value="BDD">Base de données</option>
                    <option value="Framework">Framework</option>
                    <option value="Outil">Outil</option>
                </select>
            </div>
            <button type="submit" id="competence-form-submit">Ajouter la compétence</button>
        </form>
    </div>
</div>

<script>
function ajouterCompetence() {
    document.getElementById('competence-form-titre').innerText = 'Ajouter une nouvelle compétence';
    document.getElementById('competence-form-submit').innerText = 'Ajouter la compétence';
    document.getElementById('competence-form').reset();
    document.getElementById('modal-form').style.display = 'flex';

    document.getElementById('competence-form').onsubmit = function(event) {
        event.preventDefault();
        const formData = new FormData(this);
        fetch('competencesCRUD', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            window.location.reload();
        })
        .catch(error => {
            console.error('Erreur:', error);
        });
    };
}

function editCompetence(competence) {
    document.getElementById('competence-form-titre').innerText = 'Modifier la compétence';
    document.getElementById('competence-form-submit').innerText = 'Mettre à jour la compétence';

    document.getElementById('nom').value = competence.nom;
    document.getElementById('type').value = competence.type;
    document.getElementById('modal-form').style.display = 'flex';

    document.getElementById('competence-form').onsubmit = function(event) {
        event.preventDefault();
        const formData = new FormData(this);
        formData.append('id', competence.id);
        formData.append('action', 'update');

        fetch('competencesCRUD', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            window.location.reload();
        })
        .catch(error => {
            console.error('Erreur:', error);
        });
    };
}

function previewLogo(logoPath) {
    const modal = document.getElementById('modal-preview');
    const previewImage = document.getElementById('preview-image');
    previewImage.src = logoPath;
    modal.style.display = 'flex'; // Affiche le modal
}
</script>

<?php require '../Footer.php'; ?>