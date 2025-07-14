<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $targetDir = '../images-projets/';
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $originalName = basename($_FILES['file']['name']);
    $originalPath = $targetDir . $originalName;

    $BASE_URL = 'http://localhost:' . getenv('WEB_PORT') . '/';

    if (move_uploaded_file($_FILES['file']['tmp_name'], $originalPath)) {
        $imageType = exif_imagetype($originalPath);
        $webpPath = $targetDir . pathinfo($originalName, PATHINFO_FILENAME) . '.webp';
        $webpURL  = $BASE_URL . 'images-projets/' . basename($webpPath);

        if ($imageType !== IMAGETYPE_WEBP) {
            // Convertir en WEBP
            switch ($imageType) {
                case IMAGETYPE_JPEG:
                    $image = imagecreatefromjpeg($originalPath);
                    break;
                case IMAGETYPE_PNG:
                    $image = imagecreatefrompng($originalPath);
                    // PNG transparency handling
                    imagepalettetotruecolor($image);
                    imagealphablending($image, true);
                    imagesavealpha($image, true);
                    break;
                case IMAGETYPE_GIF:
                    $image = imagecreatefromgif($originalPath);
                    break;
                default:
                    unlink($originalPath);
                    http_response_code(400);
                    echo json_encode(['error' => 'Format d\'image non pris en charge.']);
                    exit;
            }

            // Sauvegarder en WEBP
            if (imagewebp($image, $webpPath, 100)) {
                imagedestroy($image);
                unlink($originalPath); // Supprimer l'original
                echo json_encode(['location' => $webpURL]);
                exit;
            } else {
                imagedestroy($image);
                unlink($originalPath);
                http_response_code(500);
                echo json_encode(['error' => 'Erreur lors de la conversion en WEBP.']);
                exit;
            }
        } else {
            // Déjà WEBP
            echo json_encode(['location' => $BASE_URL . 'images-projets/' . $originalName]);
            exit;
        }
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Erreur lors de l\'upload de l\'image.']);
    }
    exit;
}