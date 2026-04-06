<?php
// Common authentication helpers for Cloud 9 Cafe.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function requireUser()
{
    if (!isset($_SESSION['user'])) {
        header('Location: /cloud_9_cafe/auth/login.php');
        exit;
    }
}

function requireAdmin()
{
    if (!isset($_SESSION['admin'])) {
        header('Location: /cloud_9_cafe/auth/login.php');
        exit;
    }
}

function currentUserId()
{
    return $_SESSION['user_id'] ?? null;
}
?>