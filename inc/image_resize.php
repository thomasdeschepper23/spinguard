<?php
/**
 * SpinGuard — automatische foto-optimalisatie
 * Resize bij upload naar max breedte (default 1920px) en re-encode JPEG quality 85.
 */
if (!defined('SPINGUARD_INC')) { http_response_code(403); exit('Forbidden'); }

function resize_uploaded_image($source_path, $max_width = 1920, $jpeg_quality = 85) {
    if (!extension_loaded('gd')) return false;  // GD niet beschikbaar — skip

    $info = @getimagesize($source_path);
    if (!$info) return false;
    [$width, $height, $type] = $info;

    // Niet resizen als al klein genoeg
    if ($width <= $max_width) return true;

    $new_width = $max_width;
    $new_height = (int)($height * ($max_width / $width));

    switch ($type) {
        case IMAGETYPE_JPEG: $src = @imagecreatefromjpeg($source_path); break;
        case IMAGETYPE_PNG:  $src = @imagecreatefrompng($source_path);  break;
        case IMAGETYPE_WEBP: $src = function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($source_path) : false; break;
        case IMAGETYPE_GIF:  $src = @imagecreatefromgif($source_path);  break;
        default: return false;
    }
    if (!$src) return false;

    $dst = imagecreatetruecolor($new_width, $new_height);

    // PNG/GIF/WEBP transparency behoud
    if (in_array($type, [IMAGETYPE_PNG, IMAGETYPE_WEBP, IMAGETYPE_GIF])) {
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
        $transparent = imagecolorallocatealpha($dst, 255, 255, 255, 127);
        imagefilledrectangle($dst, 0, 0, $new_width, $new_height, $transparent);
    }

    imagecopyresampled($dst, $src, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

    $ok = false;
    switch ($type) {
        case IMAGETYPE_JPEG: $ok = imagejpeg($dst, $source_path, $jpeg_quality); break;
        case IMAGETYPE_PNG:  $ok = imagepng($dst, $source_path, 8); break;
        case IMAGETYPE_WEBP: $ok = function_exists('imagewebp') ? imagewebp($dst, $source_path, $jpeg_quality) : false; break;
        case IMAGETYPE_GIF:  $ok = imagegif($dst, $source_path); break;
    }
    imagedestroy($src);
    imagedestroy($dst);
    return $ok;
}
