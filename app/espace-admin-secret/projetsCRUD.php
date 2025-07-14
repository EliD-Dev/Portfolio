<?php
require '../Header.php';

if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    echo "<script>window.location.href='../admin';</script>";
    exit;
}

/*
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
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = $_POST['titre'] ?? null;
    $description = $_POST['description'] ?? null;
    $date = $_POST['date'] ?? null;
    $type = $_POST['type'] ?? null;
    $url = $_POST['url'] ?? null;
    $contenu = $_POST['contenu'] ?? null;

    // Gestion de l'upload de l'image
    $imagePath = null;

    if (isset($_FILES['imagePath']) && $_FILES['imagePath']['error'] === UPLOAD_ERR_OK) {
        $targetDir = '../images-projets/';
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $originalName = basename($_FILES['imagePath']['name']);
        $originalPath = $targetDir . $originalName;

        // Déplacer le fichier uploadé
        if (move_uploaded_file($_FILES['imagePath']['tmp_name'], $originalPath)) {
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

                    // Mettre à jour $imagePath pour l'URL webp
                    $imagePath = BASE_URL . 'images-projets/' . basename($webpPath);
                } else {
                    // Pas pu convertir, garder le fichier original
                    $imagePath = BASE_URL . 'images-projets/' . $originalName;
                }
            } else {
                // Déjà un webp
                $imagePath = BASE_URL . 'images-projets/' . $originalName;
            }
        } else {
            $imagePath = null;
        }
    }


    if (isset($_POST['id'])) {
        // Mise à jour
        $id = $_POST['id'];
        if (isset($_POST['action']) && $_POST['action'] === 'delete') {
            $requete = $connexionDB->prepare("DELETE FROM Projets WHERE id = :id");
            $requete->bindParam(':id', $id);
            $requete->execute();
            echo "<script>window.location.href='projetsCRUD';</script>";
            exit;
        } else {
            if (!$imagePath) {
                // Si l'image n'est pas mise à jour, on récupère l'ancienne image
                $requete = $connexionDB->prepare("SELECT imagePath FROM Projets WHERE id = :id");
                $requete->bindParam(':id', $id);
                $requete->execute();
                $projet = $requete->fetch(PDO::FETCH_ASSOC);
                $imagePath = $projet['imagePath'];
            }

            $requete = $connexionDB->prepare("UPDATE Projets SET titre = :titre, description = :description, date = :date, type = :type, url = :url, imagePath = :imagePath, contenu = :contenu WHERE id = :id");
            $requete->bindParam(':titre', $titre);
            $requete->bindParam(':description', $description);
            $requete->bindParam(':date', $date);
            $requete->bindParam(':type', $type);
            $requete->bindParam(':url', $url);
            $requete->bindParam(':imagePath', $imagePath);
            $requete->bindParam(':contenu', $contenu);
            $requete->bindParam(':id', $id);
            $requete->execute();
            echo "Projet mis à jour avec succès.";
        }
    } else {
        // Ajout
        $requete = $connexionDB->prepare("INSERT INTO Projets (titre, description, date, type, url, imagePath, contenu) VALUES (:titre, :description, :date, :type, :url, :imagePath, :contenu)");
        $requete->bindParam(':titre', $titre);
        $requete->bindParam(':description', $description);
        $requete->bindParam(':date', $date);
        $requete->bindParam(':type', $type);
        $requete->bindParam(':url', $url);
        $requete->bindParam(':imagePath', $imagePath);
        $requete->bindParam(':contenu', $contenu);
        $requete->execute();
        echo "Nouveau projet ajouté avec succès.";
    }
    exit;
}
?>

<script src="https://cdn.tiny.cloud/1/<?= htmlspecialchars(getenv('TINYMCE_API_KEY')) ?>/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>

<main>
    <button class="admin-back-button" onclick="window.location.href='espaceAdminSecret';">Retour à l'espace administrateur</button>
    <h1>Gestion des projets</h1>

    <div class="bouton-ajouter-mobile">
        <button onclick="ajouterProjet();"><i class="fas fa-plus"></i> Ajouter un projet</button>
    </div>

    <table>
        <?php
        $requete = $connexionDB->prepare("SELECT * FROM Projets");
        $requete->execute();
        $projets = $requete->fetchAll(PDO::FETCH_ASSOC);
        $requete->closeCursor();
        ?>
        <thead>
            <tr>
                <th>Titre</th>
                <th>Description</th>
                <th>Date</th>
                <th>Type</th>
                <th>URL</th>
                <th>Image</th>
                <th>Contenu</th>
                <th><button onclick="ajouterProjet();"><i class="fas fa-plus"></i></button></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($projets as $projet): ?>
                <tr>
                    <td data-label="Titre"><?= htmlspecialchars($projet['titre']); ?></td>
                    <td data-label="Description"><?= htmlspecialchars($projet['description']); ?></td>
                    <td data-label="Date"><?= htmlspecialchars($projet['date']); ?></td>
                    <td data-label="Type"><?= htmlspecialchars($projet['type']); ?></td>
                    <td data-label="URL">
                        <a href="<?= htmlspecialchars($projet['url']); ?>" target="_blank" aria-label="Voir le projet : <?= htmlspecialchars($projet['titre']); ?>">
                            <?= htmlspecialchars($projet['url']) ? htmlspecialchars($projet['url']) : 'Voir le projet' ?>
                        </a>
                    </td>
                    <td data-label="Image">
                        <button onclick="previewImage('<?= htmlspecialchars($projet['imagePath']); ?>');"><i class="fas fa-image"></i></button>
                    </td>
                    <td data-label="Contenu">
                        <button onclick='viewProject(<?= json_encode($projet["contenu"]); ?>);'>
                            <i class="fas fa-eye"></i>
                        </button>
                    </td>
                    <td data-label="Actions">
                        <button onclick='editProject(<?= json_encode($projet, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>);'>
                            <i class="fas fa-edit"></i>
                        </button>
                        <form action="projetsCRUD" method="post" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce projet ?');" style="display:inline;">
                            <input type="hidden" name="id" value="<?= $projet['id']; ?>">
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

<div id="modal-content" class="modal-preview">
    <div>
        <div class="modal-right"><button class="modal-button" onclick="document.getElementById('modal-content').style.display='none';">Fermer</button></div>
        <div id="project-content"></div>
    </div>
</div>

<div id="modal-form" class="modal-form">
    <div id="form-container" class="form-container">
        <div class="modal-right"><button class="modal-button" onclick="document.getElementById('modal-form').style.display='none';">Fermer</button></div>
        <h2 id="projet-form-titre">Ajouter un nouveau projet</h2>
        <form id="projet-form" method="post" action="projetsCRUD" class="admin-login-form" enctype="multipart/form-data">
            <div class="form-group">
                <label for="titre">Titre :</label>
                <input type="text" id="titre" name="titre" required>
            </div>
            <div class="form-group">
                <label for="description">Description :</label>
                <input type="text" id="description" name="description" required>
            </div>
            <div class="form-group">
                <label for="date">Date :</label>
                <input type="date" id="date" name="date">
            </div>
            <div class="form-group">
                <label for="type">Type :</label>
                <input type="text" id="type" name="type">
            </div>
            <div class="form-group">
                <label for="url">URL :</label>
                <input type="url" id="url" name="url">
            </div>
            <div class="form-group">
                <label for="imagePath">Image Path :</label>
                <input type="file" id="imagePath" name="imagePath" accept="image/*">
            </div>
            <div class="form-group">
                <label for="contenu">Contenu :</label>
                <textarea id="contenu" name="contenu"></textarea>
            </div>
            <button type="submit" id="projet-form-submit">Ajouter le projet</button>
        </form>
    </div>
</div>

<script>
  tinymce.init({
    selector: '#contenu',
    plugins: 'advlist autolink lists link image charmap print preview hr anchor pagebreak',
    toolbar_mode: 'floating',
    toolbar: 'undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | image',
    image_title: true,
    height: 500,
    width: '100%',
    automatic_uploads: true,
    images_upload_url: 'imageProjet',
    file_picker_types: 'image',
    file_picker_callback: function(cb, value, meta) {
      var input = document.createElement('input');
      input.setAttribute('type', 'file');
      input.setAttribute('accept', 'image/*');
      input.onchange = function() {
        var file = this.files[0];
        var reader = new FileReader();
        reader.onload = function() {
          var id = 'blobid' + (new Date()).getTime();
          var blobCache = tinymce.activeEditor.editorUpload.blobCache;
          var base64 = reader.result.split(',')[1];
          var blobInfo = blobCache.create(id, file, base64);
          blobCache.add(blobInfo);
          cb(blobInfo.blobUri(), { title: file.name });
        };
        reader.readAsDataURL(file);
      };
      input.click();
    }
  });
</script>

<script>
function ajouterProjet() {
    document.getElementById('modal-form').style.display = 'flex';
    document.getElementById('projet-form-titre').textContent = 'Ajouter un nouveau projet';
    document.getElementById('projet-form-submit').textContent = 'Ajouter le projet';

    document.getElementById('projet-form').reset();
    document.getElementById('projet-form').onsubmit = function(event) {
        event.preventDefault();
        const formData = new FormData(this);
        fetch('projetsCRUD', { method: 'POST', body: formData })
            .then(response => response.text())
            .then(data => {
                window.location.reload();
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Une erreur est survenue lors de l\'ajout du projet.');
            });
    };
}

function editProject(projet) {
    document.getElementById('modal-form').style.display = 'flex';
    document.getElementById('projet-form-titre').textContent = 'Modifier le projet';
    document.getElementById('projet-form-submit').textContent = 'Mettre à jour le projet';

    document.getElementById('titre').value = projet.titre;
    document.getElementById('description').value = projet.description;
    document.getElementById('date').value = projet.date;
    document.getElementById('type').value = projet.type;
    document.getElementById('url').value = projet.url;
    tinymce.get('contenu').setContent(projet.contenu);

    document.getElementById('projet-form').onsubmit = function(event) {
        event.preventDefault();
        const formData = new FormData(this);
        formData.append('id', projet.id);

        fetch('projetsCRUD', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            window.location.reload();
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Une erreur est survenue lors de la mise à jour du projet.');
        });
    };
}

function viewProject(contenu) {
    const modal = document.getElementById('modal-content');
    const projectContent = document.getElementById('project-content');
    projectContent.innerHTML = contenu;
    modal.style.display = 'flex';
}

function previewImage(logoPath) {
    const modal = document.getElementById('modal-preview');
    const previewImage = document.getElementById('preview-image');
    previewImage.src = logoPath;
    modal.style.display = 'flex'; // Affiche le modal
}
</script>

<?php require '../Footer.php'; ?>