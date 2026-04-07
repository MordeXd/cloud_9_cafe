<?php
// Centralized image upload helpers for Cloud 9 Cafe.
// Provides strict validation, sanitized storage paths and safe filenames.

/**
 * Convert any folder name to a safe, predictable slug.
 */
function sanitizeFolderName(string $name, string $fallback = 'default'): string
{
    $slug = strtolower(preg_replace('/[^a-zA-Z0-9_-]+/', '_', $name));
    $slug = trim($slug, '_-');
    return $slug !== '' ? $slug : $fallback;
}

/**
 * Validate an uploaded image against size, extension and MIME rules.
 */
function validateImageFile(array $file, ?string &$error = null): bool
{
    $error = null;
    $maxSize = 5 * 1024 * 1024; // 5MB
    $allowedExt  = ['jpg', 'jpeg', 'png', 'webp'];
    $allowedMime = ['image/jpeg', 'image/png', 'image/webp'];

    if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
        $error = 'Upload failed. Please try again.';
        return false;
    }

    if (empty($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        $error = 'Invalid upload source.';
        return false;
    }

    if (($file['size'] ?? 0) > $maxSize) {
        $error = 'Image must be 5MB or smaller.';
        return false;
    }

    $ext = strtolower(pathinfo($file['name'] ?? '', PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExt, true)) {
        $error = 'Invalid image type. Allowed: jpg, jpeg, png, webp.';
        return false;
    }

    // Detect MIME using finfo; fall back to mime_content_type if needed.
    $mime = null;
    if (class_exists('finfo')) {
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo ? $finfo->file($file['tmp_name']) : null;
    }
    if ($mime === null && function_exists('mime_content_type')) {
        $mime = @mime_content_type($file['tmp_name']);
    }
    if ($mime === false || $mime === null || !in_array($mime, $allowedMime, true)) {
        $error = 'File is not a valid image.';
        return false;
    }

    // Additional guard against disguised executables.
    if (!@getimagesize($file['tmp_name'])) {
        $error = 'File is not a valid image.';
        return false;
    }

    return true;
}

/**
 * Validate and store an uploaded image.
 *
 * @param array  $file        The $_FILES entry.
 * @param string $typeFolder  Top-level folder inside /images (e.g., profile_photo, item_image).
 * @param string $nameForFolder Used to build a sanitized subfolder name.
 * @param string $prefix      Prefix for generated filenames.
 *
 * @return array [relativePath|null, errorMessage|null]
 */
function saveUploadedImage(array $file, string $typeFolder, string $nameForFolder, string $prefix = 'img_'): array
{
    if (empty($file['name'])) {
        return [null, 'No file selected.'];
    }

    $validationError = null;
    if (!validateImageFile($file, $validationError)) {
        return [null, $validationError];
    }

    $safeFolder = sanitizeFolderName($nameForFolder, $typeFolder);
    $baseDir = dirname(__DIR__) . "/images/{$typeFolder}/{$safeFolder}/";

    if (!is_dir($baseDir) && !mkdir($baseDir, 0755, true)) {
        return [null, 'Failed to create image directory.'];
    }

    $extension  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $uniqueName = $prefix . uniqid('', true) . '.' . $extension;
    $targetPath = $baseDir . $uniqueName;

    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        return [null, 'Unable to save uploaded image.'];
    }

    $relativePath = "images/{$typeFolder}/{$safeFolder}/{$uniqueName}";
    return [$relativePath, null];
}
?>
