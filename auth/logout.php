<?php
/**
 * =============================================================================
 * CLOUD 9 CAFE - LOGOUT SCRIPT
 * =============================================================================
 * 
 * ROLE: Handles user and admin logout by clearing the authentication cookie.
 *       Works for both user and admin sessions since they use the same cookie.
 * 
 * USED BY: Clicking "Logout" in navbar dropdown or sidebar
 * 
 * FLOW: 1. Include database config (which loads TokenAuth)
 *       2. Call logout() method to clear auth cookie
 *       3. Redirect to login page
 * 
 * NOTE: This works for both users and admins as they share the same
 *       auth_token cookie structure.
 */

// =============================================================================
// SECTION: Authentication Include (TokenAuth only)
// =============================================================================
require_once '../config/TokenAuth.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$auth = new TokenAuth();
// =============================================================================
// END SECTION: Authentication Include
// =============================================================================

// =============================================================================
// SECTION: Logout Processing
// DESCRIPTION: Clear authentication cookie
// =============================================================================

// Clear the auth cookie using TokenAuth
// FUNCTION: $auth->logout() - Sets cookie with past expiry to delete it
$auth->logout();

// Clear legacy session flags as well
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$_SESSION = [];
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();
// =============================================================================
// END SECTION: Logout Processing
// =============================================================================

// =============================================================================
// SECTION: Redirect
// DESCRIPTION: Redirect to login page after logout
// =============================================================================

// Redirect to home page after logout
header("Location: /cloud_9_cafe/");

// Stop script execution
// FUNCTION: exit() - Terminates script execution
exit();
// =============================================================================
// END SECTION: Redirect
// =============================================================================
?>
