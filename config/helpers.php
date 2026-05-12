<?php
/**
 * Compress image and resize if width > 1200px
 * 
 * @param string $source Path to source image
 * @param string $destination Path to save compressed image
 * @param int $quality Compression quality (0-100)
 * @return bool Success status
 */
function compressImage($source, $destination, $quality = 75) {
    // Check if GD extension is loaded
    if (!function_exists('imagecreatefromjpeg')) {
        return move_uploaded_file($source, $destination);
    }

    $info = getimagesize($source);
    if ($info === false) return move_uploaded_file($source, $destination);

    // Create image from source based on type
    switch ($info['mime']) {
        case 'image/jpeg':
            $image = imagecreatefromjpeg($source);
            break;
        case 'image/gif':
            $image = imagecreatefromgif($source);
            break;
        case 'image/png':
            $image = imagecreatefrompng($source);
            imagepalettetotruecolor($image);
            imagealphablending($image, true);
            imagesavealpha($image, true);
            break;
        case 'image/webp':
            $image = imagecreatefromwebp($source);
            break;
        default:
            return move_uploaded_file($source, $destination);
    }

    // Resize if too large (max width 1200px)
    $max_width = 1200;
    $width = imagesx($image);
    $height = imagesy($image);
    
    if ($width > $max_width) {
        $new_width = $max_width;
        $new_height = ($height / $width) * $new_width;
        $tmp = imagecreatetruecolor($new_width, $new_height);
        
        // Handle transparency for PNG/WEBP
        if ($info['mime'] == 'image/png' || $info['mime'] == 'image/webp') {
            imagealphablending($tmp, false);
            imagesavealpha($tmp, true);
            $transparent = imagecolorallocatealpha($tmp, 0, 0, 0, 127);
            imagefill($tmp, 0, 0, $transparent);
        }

        imagecopyresampled($tmp, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
        imagedestroy($image);
        $image = $tmp;
    }

    // Save compressed image
    $success = false;
    switch ($info['mime']) {
        case 'image/jpeg':
            $success = imagejpeg($image, $destination, $quality);
            break;
        case 'image/gif':
            $success = imagegif($image, $destination);
            break;
        case 'image/png':
            // PNG quality is 0-9
            $png_quality = 9 - round(($quality / 100) * 9);
            $success = imagepng($image, $destination, $png_quality);
            break;
        case 'image/webp':
            $success = imagewebp($image, $destination, $quality);
            break;
    }

    imagedestroy($image);
    return $success;
}
