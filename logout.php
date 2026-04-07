<?php
// Simple logout file - clear auth cookie + session and return to home
require_once __DIR__ . '/config/TokenAuth.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$auth = new TokenAuth();

// Clear auth cookie
$auth->logout();

// Clear all session data
$_SESSION = [];

// Expire the session cookie if one exists
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

// Destroy the session
session_destroy();

// Redirect to the public homepage
header('Location: /cloud_9_cafe/');
exit;
?>
