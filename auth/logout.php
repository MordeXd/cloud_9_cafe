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
// SECTION: Database & Authentication Include
// DESCRIPTION: Include config to access TokenAuth instance
// =============================================================================
require_once '../config/db_config.php';
// =============================================================================
// END SECTION: Database & Authentication Include
// =============================================================================

// =============================================================================
// SECTION: Logout Processing
// DESCRIPTION: Clear authentication cookie
// =============================================================================

// Clear the auth cookie using TokenAuth
// FUNCTION: $auth->logout() - Sets cookie with past expiry to delete it
$auth->logout();
// =============================================================================
// END SECTION: Logout Processing
// =============================================================================

// =============================================================================
// SECTION: Redirect
// DESCRIPTION: Redirect to login page after logout
// =============================================================================

// Redirect to login page
// FUNCTION: header() - Sends HTTP Location header for redirection
header("Location: login.php");

// Stop script execution
// FUNCTION: exit() - Terminates script execution
exit();
// =============================================================================
// END SECTION: Redirect
// =============================================================================
?>
