<?php
/**
 * Cloud 9 Cafe - Configuration Check
 * Run this to verify your setup
 */

header('Content-Type: text/plain');

echo "=========================================\n";
echo "  CLOUD 9 CAFE - SETUP VERIFICATION\n";
echo "=========================================\n\n";

$errors = [];
$warnings = [];

// Check PHP version
echo "[1] Checking PHP Version...\n";
if (PHP_VERSION_ID >= 70400) {
    echo "    ✓ PHP " . PHP_VERSION . " (OK)\n";
} else {
    $errors[] = "PHP 7.4 or higher required";
    echo "    ✗ PHP " . PHP_VERSION . " (Too old)\n";
}

// Check .env file
echo "\n[2] Checking .env file...\n";
$envPath = __DIR__ . '/../.env';
if (file_exists($envPath)) {
    echo "    ✓ .env file exists\n";
} else {
    $errors[] = ".env file not found. Copy .env.example to .env";
    echo "    ✗ .env file NOT found\n";
}

// Check required extensions
echo "\n[3] Checking PHP Extensions...\n";
$required = ['mbstring', 'gd', 'json'];
foreach ($required as $ext) {
    if (extension_loaded($ext)) {
        echo "    ✓ $ext loaded\n";
    } else {
        $warnings[] = "$ext extension not loaded";
        echo "    ⚠ $ext NOT loaded\n";
    }
}

// Check JSON Database
echo "\n[4] Checking JSON Database...\n";
$dataDir = __DIR__ . '/../data/';
if (!is_dir($dataDir)) {
    mkdir($dataDir, 0755, true);
    echo "    ✓ Created data directory\n";
}

if (is_writable($dataDir)) {
    echo "    ✓ Data directory is writable\n";
} else {
    $errors[] = "Data directory is not writable";
    echo "    ✗ Data directory is NOT writable\n";
}

// Initialize database if needed
try {
    require_once 'JsonDB.php';
    echo "    ✓ JsonDB class loaded\n";
    
    // Check if data files exist
    $tables = ['cafe_users', 'cafe_admins', 'menu_items', 'cafe_orders', 'cafe_order_items', 'cafe_cart', 'cafe_offers', 'contact_messages', 'user_addresses'];
    foreach ($tables as $table) {
        $file = $dataDir . $table . '.json';
        if (file_exists($file)) {
            echo "    ✓ $table.json exists\n";
        } else {
            echo "    ⚠ $table.json will be created on first use\n";
        }
    }
} catch (Exception $e) {
    $errors[] = "Error initializing JsonDB: " . $e->getMessage();
    echo "    ✗ Error: " . $e->getMessage() . "\n";
}

// Check folder permissions
echo "\n[5] Checking Folder Permissions...\n";
$folders = [
    'assets/uploads' => __DIR__ . '/../assets/uploads',
    'data' => $dataDir,
];
foreach ($folders as $name => $path) {
    if (is_writable($path)) {
        echo "    ✓ $name is writable\n";
    } else {
        $warnings[] = "$name is not writable";
        echo "    ⚠ $name is NOT writable\n";
    }
}

// Check session
echo "\n[6] Checking Session...\n";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
echo "    ✓ Session working\n";

// Summary
echo "\n=========================================\n";
if (empty($errors) && empty($warnings)) {
    echo "  ✓ ALL CHECKS PASSED!\n";
    echo "  You can now use the application.\n";
} else {
    if (!empty($errors)) {
        echo "  ✗ ERRORS FOUND:\n";
        foreach ($errors as $error) {
            echo "    - $error\n";
        }
    }
    if (!empty($warnings)) {
        echo "  ⚠ WARNINGS:\n";
        foreach ($warnings as $warning) {
            echo "    - $warning\n";
        }
    }
}
echo "=========================================\n";

// Next steps
if (empty($errors)) {
    echo "\n📋 NEXT STEPS:\n";
    echo "1. Access website: http://localhost/cloud_9_cafe/\n";
    echo "2. Default Admin Login: admin@cloud9cafe.com / admin123\n";
    echo "3. All data is stored in JSON files in ./data/ directory\n";
}
?>
