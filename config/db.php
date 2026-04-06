<?php
// Cloud 9 Cafe - Database connection file
// Reads DB settings from .env (if present) so local overrides work without code edits.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Lightweight .env reader (no external dependency, tolerant of comments/formatting)
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
        // Strip optional quotes
        if ((str_starts_with($value, '"') && str_ends_with($value, '"')) ||
            (str_starts_with($value, "'") && str_ends_with($value, "'"))) {
            $value = substr($value, 1, -1);
        }
        $env[$key] = $value;
    }
    return $env;
}

$env = loadEnv(__DIR__ . '/../.env');

// Fall back to previous defaults if env values are missing
$host     = $env['DB_HOST'] ?? 'localhost';
$username = $env['DB_USERNAME'] ?? 'cloud9_user';
$password = $env['DB_PASSWORD'] ?? 'cloud9cafe';
$database = $env['DB_DATABASE'] ?? 'cloud_9_cafe';

$con = mysqli_connect($host, $username, $password, $database);
if (!$con) {
    die('Database connection failed: ' . mysqli_connect_error());
}

date_default_timezone_set($env['APP_TIMEZONE'] ?? 'Asia/Kolkata');

// Helper function to clean incoming data.
function cleanInput($value)
{
    global $con;
    return mysqli_real_escape_string($con, trim($value));
}
?>
