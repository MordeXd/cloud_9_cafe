<?php
// Cloud 9 Cafe database setup file
// Run this file once in browser or CLI to create the database and tables.

// Load env for DB creds if available (tolerant parser)
function loadEnv($path)
{
    if (!file_exists($path)) {
        return [];
    }

    $env = [];
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $trimmed = ltrim($line);
        if ($trimmed === '' || $trimmed[0] === '#' || $trimmed[0] === ';') {
            continue;
        }
        $delimiterPos = strpos($trimmed, '=');
        if ($delimiterPos === false) {
            continue;
        }
        $key = trim(substr($trimmed, 0, $delimiterPos));
        $value = trim(substr($trimmed, $delimiterPos + 1));
        if ((str_starts_with($value, '"') && str_ends_with($value, '"')) ||
            (str_starts_with($value, "'") && str_ends_with($value, "'"))) {
            $value = substr($value, 1, -1);
        }
        $env[$key] = $value;
    }
    return $env;
}

$env = loadEnv(__DIR__ . '/.env');

$host = $env['DB_HOST'] ?? 'localhost';
$username = $env['DB_USERNAME'] ?? 'cloud9_user';
$password = $env['DB_PASSWORD'] ?? 'cloud9cafe';
$database = $env['DB_DATABASE'] ?? 'cloud_9_cafe';

$con = mysqli_connect($host, $username, $password);
if (!$con) {
    die('Connection failed: ' . mysqli_connect_error());
}

$queries = [];
$queries[] = "CREATE DATABASE IF NOT EXISTS `$database`";
$queries[] = "USE `$database`";
$queries[] = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(15) NOT NULL,
    password VARCHAR(255) NOT NULL,
    gender VARCHAR(20) DEFAULT NULL,
    profile_image VARCHAR(255) DEFAULT NULL,
    activation_token VARCHAR(64) DEFAULT NULL,
    is_verified TINYINT(1) NOT NULL DEFAULT 0,
    reset_token VARCHAR(64) DEFAULT NULL,
    reset_expires DATETIME DEFAULT NULL,
    role ENUM('user','admin') NOT NULL DEFAULT 'user',
    status ENUM('active','inactive') NOT NULL DEFAULT 'inactive',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$queries[] = "CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(150) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$queries[] = "CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(100) NOT NULL,
    status ENUM('active','inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$queries[] = "CREATE TABLE IF NOT EXISTS menu_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT DEFAULT NULL,
    item_name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    item_image VARCHAR(255) DEFAULT NULL,
    status ENUM('active','inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_menu_category FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
)";
$queries[] = "CREATE TABLE IF NOT EXISTS reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    reservation_date DATE NOT NULL,
    reservation_time TIME NOT NULL,
    guest_count INT NOT NULL,
    occasion VARCHAR(50) DEFAULT NULL,
    special_request TEXT DEFAULT NULL,
    status ENUM('pending','confirmed','cancelled') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_reservation_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)";
$queries[] = "CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    order_status ENUM('pending','preparing','completed','cancelled') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_order_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)";
$queries[] = "CREATE TABLE IF NOT EXISTS feedback (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    subject VARCHAR(100) NOT NULL,
    rating INT NOT NULL,
    message TEXT NOT NULL,
    reply_message TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_feedback_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)";
$queries[] = "CREATE TABLE IF NOT EXISTS cafe_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cafe_name VARCHAR(100) NOT NULL,
    contact_email VARCHAR(100) NOT NULL,
    phone VARCHAR(15) NOT NULL,
    logo VARCHAR(255) DEFAULT NULL,
    address TEXT NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";
$queries[] = "CREATE TABLE IF NOT EXISTS cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    menu_item_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_cart_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_cart_menu_item FOREIGN KEY (menu_item_id) REFERENCES menu_items(id) ON DELETE CASCADE,
    UNIQUE KEY unique_cart_item (user_id, menu_item_id)
)";

$successLog = [];
$errorLog = [];
foreach ($queries as $query) {
    if (mysqli_query($con, $query)) {
        $successLog[] = $query;
    } else {
        $errorLog[] = mysqli_error($con);
    }
}

// Ensure new columns exist for activation/reset flows (idempotent checks)
$columnChecks = [
    "SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='$database' AND TABLE_NAME='users' AND COLUMN_NAME='activation_token'",
    "SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='$database' AND TABLE_NAME='users' AND COLUMN_NAME='is_verified'",
    "SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='$database' AND TABLE_NAME='users' AND COLUMN_NAME='reset_token'",
    "SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='$database' AND TABLE_NAME='users' AND COLUMN_NAME='reset_expires'"
];

$alterStatements = [
    "ALTER TABLE users ADD COLUMN activation_token VARCHAR(64) DEFAULT NULL",
    "ALTER TABLE users ADD COLUMN is_verified TINYINT(1) NOT NULL DEFAULT 0",
    "ALTER TABLE users ADD COLUMN reset_token VARCHAR(64) DEFAULT NULL",
    "ALTER TABLE users ADD COLUMN reset_expires DATETIME DEFAULT NULL"
];

foreach ($columnChecks as $index => $checkSql) {
    $check = mysqli_query($con, $checkSql);
    if ($check && mysqli_num_rows($check) === 0) {
        mysqli_query($con, $alterStatements[$index]);
    }
}

$settingsCheck = mysqli_query($con, "SELECT id FROM cafe_settings LIMIT 1");
if ($settingsCheck && mysqli_num_rows($settingsCheck) === 0) {
    mysqli_query($con, "INSERT INTO cafe_settings (cafe_name, contact_email, phone, address) VALUES ('Cloud 9 Cafe', 'hello@cloud9cafe.local', '9999999999', 'Main Street, Your City')");
}

// Create default admin if missing
$adminEmail = $env['ADMIN_EMAIL'] ?? 'admin@cloud9cafe.com';
$adminPassword = $env['ADMIN_PASSWORD'] ?? 'change_this_password';
$adminCheck = mysqli_query($con, "SELECT id FROM users WHERE email='" . mysqli_real_escape_string($con, $adminEmail) . "' LIMIT 1");
if ($adminCheck && mysqli_num_rows($adminCheck) === 0) {
    $hashedAdminPassword = password_hash($adminPassword, PASSWORD_DEFAULT);
    $adminInsert = "INSERT INTO users (full_name, email, phone, password, gender, role, status, is_verified)
                    VALUES ('Cafe Admin', '$adminEmail', '9999999999', '$hashedAdminPassword', 'other', 'admin', 'active', 1)";
    mysqli_query($con, $adminInsert);
}

// Seed default categories
$defaultCategories = ['Coffee', 'Tea', 'Pastry', 'Sandwich', 'Dessert'];
foreach ($defaultCategories as $cat) {
    $catSafe = mysqli_real_escape_string($con, $cat);
    mysqli_query($con, "INSERT IGNORE INTO categories (category_name, status) VALUES ('$catSafe', 'active')");
}

// Seed default menu items if not present
$menuSeeds = [
    ['Cappuccino', 'Rich espresso with steamed milk foam.', 3.50, 'Coffee'],
    ['Latte', 'Smooth espresso with steamed milk.', 3.75, 'Coffee'],
    ['Cold Brew', 'Slow-steeped cold brew coffee.', 4.25, 'Coffee'],
    ['Green Tea', 'Light and refreshing hot green tea.', 2.75, 'Tea'],
    ['Masala Chai', 'Spiced Indian milk tea.', 3.00, 'Tea'],
    ['Butter Croissant', 'Flaky, buttery classic croissant.', 2.50, 'Pastry'],
    ['Chocolate Muffin', 'Moist muffin with chocolate chips.', 2.75, 'Pastry'],
    ['Veg Sandwich', 'Grilled veggie sandwich with cheese.', 4.50, 'Sandwich'],
    ['Chicken Panini', 'Grilled chicken panini with pesto.', 5.25, 'Sandwich'],
    ['Cheesecake', 'Creamy classic cheesecake slice.', 4.00, 'Dessert'],
];

foreach ($menuSeeds as $seed) {
    [$itemName, $desc, $price, $catName] = $seed;
    $itemSafe = mysqli_real_escape_string($con, $itemName);
    $descSafe = mysqli_real_escape_string($con, $desc);
    $catSafe = mysqli_real_escape_string($con, $catName);
    $priceVal = (float)$price;

    $catIdRes = mysqli_query($con, "SELECT id FROM categories WHERE category_name='$catSafe' LIMIT 1");
    $catRow = $catIdRes ? mysqli_fetch_assoc($catIdRes) : null;
    if (!$catRow) {
        continue;
    }
    $catId = (int)$catRow['id'];

    $exists = mysqli_query($con, "SELECT id FROM menu_items WHERE item_name='$itemSafe' LIMIT 1");
    if ($exists && mysqli_num_rows($exists) > 0) {
        continue;
    }

    mysqli_query($con, "INSERT INTO menu_items (category_id, item_name, description, price, status) VALUES ($catId, '$itemSafe', '$descSafe', $priceVal, 'active')");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cloud 9 Cafe Database Setup</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="card shadow-sm">
            <div class="card-body">
                <h1 class="h3 mb-3">Cloud 9 Cafe Database Setup</h1>
                <p class="text-muted">This file creates the database and tables for the project.</p>
                <h2 class="h5 mt-4">Successful Queries</h2>
                <ul>
                    <?php foreach ($successLog as $item): ?>
                        <li><code><?= htmlspecialchars($item) ?></code></li>
                    <?php endforeach; ?>
                </ul>
                <h2 class="h5 mt-4">Errors</h2>
                <?php if (!empty($errorLog)): ?>
                    <ul class="text-danger">
                        <?php foreach ($errorLog as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="text-success">No errors found. Database setup completed.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
