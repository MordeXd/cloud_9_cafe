<?php
/**
 * =============================================================================
 * CLOUD 9 CAFE - DATABASE CONFIGURATION
 * =============================================================================
 * 
 * ROLE: This file initializes the database connection and authentication system.
 *       It sets up the JSON-based database (JsonDB) and cookie-based 
 *       authentication (TokenAuth) for use across the application.
 * 
 * USED BY: Almost all PHP files in the project via include/require
 * 
 * FLOW: 1. Requires JsonDB class file
 *       2. Requires TokenAuth class file
 *       3. Creates data directory if it doesn't exist
 *       4. Initializes JsonDB instance
 *       5. Initializes TokenAuth instance
 *       6. Creates default admin if no admins exist
 * 
 * NOTE: Uses JSON files instead of MySQL for this demo project
 */

// =============================================================================
// SECTION: Class File Includes
// DESCRIPTION: Require the database and authentication class files
// =============================================================================

// Require JsonDB class - Provides JSON file-based database operations
// PATH: __DIR__ = current directory (config/), then go up to JsonDB.php
require_once __DIR__ . '/JsonDB.php';

// Require TokenAuth class - Provides cookie-based authentication
require_once __DIR__ . '/TokenAuth.php';
// =============================================================================
// END SECTION: Class File Includes
// =============================================================================

// =============================================================================
// SECTION: Data Directory Setup
// DESCRIPTION: Create data directory for JSON files if it doesn't exist
// =============================================================================

// Define path to data directory (parent of config folder + /data/)
$dataDir = __DIR__ . '/../data/';

// Check if directory exists, create if not
// FUNCTION: is_dir() - Checks if directory exists
// FUNCTION: mkdir() - Creates directory with permissions
if (!is_dir($dataDir)) {
    // Create directory with 0755 permissions (rwxr-xr-x)
    // true = recursive creation (creates parent dirs if needed)
    mkdir($dataDir, 0755, true);
}
// =============================================================================
// END SECTION: Data Directory Setup
// =============================================================================

// =============================================================================
// SECTION: Database Instance Creation
// DESCRIPTION: Create global JsonDB instance for database operations
// =============================================================================

// Create new JsonDB instance
// PARAMETER: $dataDir = directory where JSON files will be stored
// AVAILABLE TABLES (JSON files):
//   - cafe_users.json      (customer accounts)
//   - cafe_admins.json     (admin accounts)
//   - cafe_cart.json       (shopping cart items)
//   - cafe_orders.json     (orders)
//   - cafe_order_items.json (order line items)
//   - menu_items.json      (menu/catalog items)
//   - cafe_messages.json   (contact form messages)
$db = new JsonDB($dataDir);
// =============================================================================
// END SECTION: Database Instance Creation
// =============================================================================

// =============================================================================
// SECTION: Authentication Instance Creation
// DESCRIPTION: Create global TokenAuth instance for cookie-based auth
// =============================================================================

// Create new TokenAuth instance
// This will be used to check login status, set cookies, and logout users
// FUNCTIONALITY:
//   - loginUser()    - Sets user auth cookie
//   - loginAdmin()   - Sets admin auth cookie
//   - logout()       - Clears auth cookie
//   - isUserLoggedIn()  - Check if user is logged in
//   - isAdminLoggedIn() - Check if admin is logged in
//   - getUserId()    - Get logged-in user's ID
//   - getAdminId()   - Get logged-in admin's ID
$auth = new TokenAuth();
// =============================================================================
// END SECTION: Authentication Instance Creation
// =============================================================================

// =============================================================================
// SECTION: Default Admin Initialization
// DESCRIPTION: Create default admin account if no admins exist in database
// =============================================================================

// Check if any admins exist
// FUNCTION: $db->count() - Returns number of records in table
// PARAMETER: 'cafe_admins' = table name
if ($db->count('cafe_admins') === 0) {
    
    // No admins found - insert default admin
    // FUNCTION: $db->insert() - Creates new record in table
    // PARAMETERS: table name, associative array of field => value
    $db->insert('cafe_admins', [
        'fullname' => 'Admin User',              // Admin display name
        'email' => 'admin@cloud9cafe.com',       // Admin login email
        'password' => 'admin123',                // Admin password (plain text for demo)
        'mobile' => '9876543210',                // Contact number
        'role' => 'super_admin',                 // Admin role (super_admin, manager, staff)
        'status' => 'Active'                     // Account status
    ]);
    // Note: Default admin credentials - Email: admin@cloud9cafe.com, Password: admin123
}
// =============================================================================
// END SECTION: Default Admin Initialization
// =============================================================================

// =============================================================================
// SECTION: Default User Initialization
// DESCRIPTION: Create default user account if no users exist in database
// =============================================================================

// Check if any users exist
// FUNCTION: $db->count() - Returns number of records in table
if ($db->count('cafe_users') === 0) {
    
    // No users found - insert default demo user
    // FUNCTION: $db->insert() - Creates new record in table
    $db->insert('cafe_users', [
        'fullname' => 'Demo User',                 // User display name
        'email' => 'user@cloud9cafe.com',          // User login email
        'password' => 'user123',                   // User password (plain text for demo)
        'mobile' => '9876543211',                  // Contact number
        'address' => '123 Coffee Street, Cafe City', // Default delivery address
        'role' => 'User',                          // User role
        'status' => 'Active',                      // Account status
        'reward_points' => 50,                     // Starting reward points
        'profile_picture' => ''                    // No profile picture yet
    ]);
    // Note: Default user credentials - Email: user@cloud9cafe.com, Password: user123
}
// =============================================================================
// END SECTION: Default User Initialization
// =============================================================================
?>
