<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $targetDir = __DIR__ . '/../images-projets/';
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    // Original filename and sanitization
    $originalName = basename($_FILES['file']['name']);
    $nameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);
    $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

    // Replace spaces with underscores
    $safeName = str_replace(' ', '_', $nameWithoutExt);

    // Define final filename and path (always use .webp)
    $finalName = $safeName . '.webp';
    $targetFile = $targetDir . $finalName;

    // Temporary upload path
    $tmpFile = $_FILES['file']['tmp_name'];

    // Convert to WebP if not already WebP
    if ($extension !== 'webp') {
        // Load image based on original extension
        switch ($extension) {
            case 'jpeg':
            case 'jpg':
                $image = imagecreatefromjpeg($tmpFile);
                break;
            case 'png':
                $image = imagecreatefrompng($tmpFile);
                // Preserve PNG transparency
                imagepalettetotruecolor($image);
                imagealphablending($image, true);
                imagesavealpha($image, true);
                break;
            case 'gif':
                $image = imagecreatefromgif($tmpFile);
                break;
            default:
                http_response_code(400);
                echo json_encode(['error' => 'Format d\'image non supporté.']);
                exit;
        }
        // Save as WebP with quality 100
        if (!imagewebp($image, $targetFile, 100)) {
            http_response_code(500);
            echo json_encode(['error' => 'Échec de la conversion en WebP.']);
            imagedestroy($image);
            exit;
        }
        imagedestroy($image);
    } else {
        // Already WebP: just move and rename
        if (!move_uploaded_file($tmpFile, $targetFile)) {
            http_response_code(500);
            echo json_encode(['error' => 'Échec du déplacement du fichier WebP.']);
            exit;
        }
    }

    // Build accessible URL
    $baseUrl = 'http://localhost:' . getenv('WEB_PORT') . '/images-projets/';
    $imageUrl = $baseUrl . $finalName;

    // Return JSON response
    echo json_encode(['location' => $imageUrl]);
    exit;
}