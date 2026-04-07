<?php
// Migrates legacy uploads/ files into the new images/{profile_photo|item_image}/... structure
// and rewrites DB paths to the stored relative path.
//
// Usage:
//   php tools/migrate_uploads.php --dry-run   (default; no changes)
//   php tools/migrate_uploads.php --run       (perform moves + DB updates)

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/upload_helpers.php';

set_time_limit(0);

$legacyDir = __DIR__ . '/../uploads/';
$imagesBase = __DIR__ . '/../images/';
$dryRun = !in_array('--run', $argv, true);

if (!is_dir($legacyDir)) {
    echo "Legacy directory not found: {$legacyDir}\n";
    exit(0);
}

if (!is_dir($imagesBase) && !$dryRun) {
    mkdir($imagesBase, 0755, true);
}

function moveLegacyFile(string $src, string $typeFolder, string $folderName, string $prefix, bool $dryRun): ?string
{
    global $imagesBase;

    if (!file_exists($src)) {
        return null;
    }

    $safeFolder = sanitizeFolderName($folderName, $typeFolder);
    $targetDir = $imagesBase . "{$typeFolder}/{$safeFolder}/";

    $ext = strtolower(pathinfo($src, PATHINFO_EXTENSION));
    $uniqueName = $prefix . uniqid('', true) . '.' . $ext;
    $targetPath = $targetDir . $uniqueName;

    if (!$dryRun) {
        if (!is_dir($targetDir) && !mkdir($targetDir, 0755, true)) {
            return null;
        }
        if (!rename($src, $targetPath)) {
            return null;
        }
    }

    return "images/{$typeFolder}/{$safeFolder}/{$uniqueName}";
}

function isAlreadyNewPath(?string $path): bool
{
    if (empty($path)) {
        return false;
    }
    $normalized = ltrim($path, '/');
    return str_starts_with($normalized, 'images/') || str_starts_with($normalized, 'http');
}

// Migrate user profile images
$userRes = mysqli_query($con, "SELECT id, full_name, profile_image FROM users WHERE profile_image IS NOT NULL AND profile_image != ''");
$userMoved = 0;
$userSkipped = 0;

if ($userRes) {
    while ($row = mysqli_fetch_assoc($userRes)) {
        $path = $row['profile_image'];
        if (isAlreadyNewPath($path)) {
            $userSkipped++;
            continue;
        }
        $src = $legacyDir . basename($path);
        $newRel = moveLegacyFile($src, 'profile_photo', $row['full_name'] ?? ('user_' . $row['id']), 'profile_', $dryRun);
        if ($newRel) {
            $userMoved++;
            if (!$dryRun) {
                $safePath = mysqli_real_escape_string($con, $newRel);
                mysqli_query($con, "UPDATE users SET profile_image='{$safePath}' WHERE id={$row['id']} LIMIT 1");
            }
            echo "[user] ID {$row['id']} -> {$newRel}" . ($dryRun ? " (dry-run)\n" : "\n");
        } else {
            echo "[user] ID {$row['id']} skipped (missing file or write failure)\n";
        }
    }
}

// Migrate menu item images
$itemRes = mysqli_query($con, "SELECT id, item_name, item_image FROM menu_items WHERE item_image IS NOT NULL AND item_image != ''");
$itemMoved = 0;
$itemSkipped = 0;

if ($itemRes) {
    while ($row = mysqli_fetch_assoc($itemRes)) {
        $path = $row['item_image'];
        if (isAlreadyNewPath($path)) {
            $itemSkipped++;
            continue;
        }
        $src = $legacyDir . basename($path);
        $newRel = moveLegacyFile($src, 'item_image', $row['item_name'] ?? ('item_' . $row['id']), 'item_', $dryRun);
        if ($newRel) {
            $itemMoved++;
            if (!$dryRun) {
                $safePath = mysqli_real_escape_string($con, $newRel);
                mysqli_query($con, "UPDATE menu_items SET item_image='{$safePath}' WHERE id={$row['id']} LIMIT 1");
            }
            echo "[item] ID {$row['id']} -> {$newRel}" . ($dryRun ? " (dry-run)\n" : "\n");
        } else {
            echo "[item] ID {$row['id']} skipped (missing file or write failure)\n";
        }
    }
}

echo "Done. Dry-run: " . ($dryRun ? 'yes' : 'no') . "\n";
echo "Users moved: {$userMoved}, skipped: {$userSkipped}\n";
echo "Items moved: {$itemMoved}, skipped: {$itemSkipped}\n";
