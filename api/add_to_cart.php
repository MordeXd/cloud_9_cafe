<?php
/**
 * =============================================================================
 * CLOUD 9 CAFE - ADD TO CART API ENDPOINT
 * =============================================================================
 * 
 * ROLE: AJAX endpoint for adding items to the shopping cart.
 *       Called via JavaScript fetch/XHR when user clicks "Add to Cart".
 *       Returns JSON response indicating success/failure.
 * 
 * USED BY: JavaScript on pages/index.php (Popular Picks section)
 *          Can be used by any page with add-to-cart functionality
 * 
 * FLOW: 1. Set JSON response header
 *       2. Include database config
 *       3. Check if user is logged in (via cookie auth)
 *       4. If not logged in → return redirect URL
 *       5. Parse JSON POST data
 *       6. Validate item exists in menu
 *       7. Check if item already in cart → update quantity
 *       8. Or insert new cart item
 *       9. Calculate new cart count
 *       10. Return success response with cart count
 * 
 * REQUEST METHOD: POST
 * CONTENT TYPE: application/json
 * RESPONSE TYPE: application/json
 */

// =============================================================================
// SECTION: Response Header Setup
// DESCRIPTION: Set content type to JSON for API response
// =============================================================================

// Set JSON response header
// FUNCTION: header() - Sends HTTP Content-Type header
header('Content-Type: application/json');
// =============================================================================
// END SECTION: Response Header Setup
// =============================================================================

// =============================================================================
// SECTION: Database & Authentication Include
// DESCRIPTION: Include database config which initializes JsonDB and TokenAuth
// =============================================================================
require_once '../config/db_config.php';
// =============================================================================
// END SECTION: Database & Authentication Include
// =============================================================================

// =============================================================================
// SECTION: Authentication Check
// DESCRIPTION: Verify user is logged in, return redirect if not
// =============================================================================

// Check if user is logged in using cookie-based authentication
// FUNCTION: $auth->isUserLoggedIn() - Returns true if valid user auth cookie exists
if (!$auth->isUserLoggedIn()) {
    
    // User not logged in - return JSON with redirect URL
    // Client-side JavaScript will handle the redirect
    echo json_encode([
        'success' => false,
        'message' => 'Please login to add items to cart',
        'redirect' => '../auth/login.php'  // URL to redirect to
    ]);
    exit();  // Stop script execution
}
// =============================================================================
// END SECTION: Authentication Check
// =============================================================================

// =============================================================================
// SECTION: Request Data Parsing
// DESCRIPTION: Parse JSON data from POST request body
// =============================================================================

// Get raw POST data from request body
// FUNCTION: file_get_contents('php://input') - Reads raw request body
// FUNCTION: json_decode() - Parses JSON string to PHP array
// true = return associative array
$data = json_decode(file_get_contents('php://input'), true);

// Validate request data
// Check if data exists and contains required 'item_id' field
if (!$data || !isset($data['item_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request - item_id is required'
    ]);
    exit();
}
// =============================================================================
// END SECTION: Request Data Parsing
// =============================================================================

// =============================================================================
// SECTION: Data Extraction
// DESCRIPTION: Extract and sanitize input data
// =============================================================================

// Get current logged-in user's ID from auth cookie
// FUNCTION: $auth->getUserId() - Returns user ID from auth token
$user_id = $auth->getUserId();

// Get menu item ID from request and convert to integer
// FUNCTION: intval() - Converts value to integer (sanitization)
$menu_item_id = intval($data['item_id']);

// Get quantity (default to 1 if not specified)
$quantity = intval($data['quantity'] ?? 1);
// =============================================================================
// END SECTION: Data Extraction
// =============================================================================

// =============================================================================
// SECTION: Item Validation
// DESCRIPTION: Verify the menu item exists in database
// =============================================================================

// Fetch menu item from database
// FUNCTION: $db->selectOne() - Gets single record matching conditions
// PARAMETERS: 'menu_items' = table, ['id' => $menu_item_id] = WHERE clause
$item = $db->selectOne('menu_items', ['id' => $menu_item_id]);

// Check if item exists
if (!$item) {
    echo json_encode([
        'success' => false,
        'message' => 'Item not found in menu'
    ]);
    exit();
}
// =============================================================================
// END SECTION: Item Validation
// =============================================================================

// =============================================================================
// SECTION: Cart Update Logic
// DESCRIPTION: Add item to cart or update quantity if already exists
// =============================================================================

// Check if this item is already in user's cart
// FUNCTION: $db->selectOne() - Check for existing cart item
$existing = $db->selectOne('cafe_cart', [
    'user_id' => $user_id, 
    'menu_item_id' => $menu_item_id
]);

if ($existing) {
    // Item already in cart - update quantity
    $newQty = $existing['quantity'] + $quantity;
    
    // Update cart item quantity
    // FUNCTION: $db->update() - Updates existing record
    // PARAMETERS: table, new data, where conditions
    $db->update('cafe_cart', ['quantity' => $newQty], ['id' => $existing['id']]);
    
} else {
    // Item not in cart - insert new cart item
    // FUNCTION: $db->insert() - Creates new record
    // PARAMETERS: table, data array
    $db->insert('cafe_cart', [
        'user_id' => $user_id,           // Who owns this cart item
        'menu_item_id' => $menu_item_id, // Which menu item
        'quantity' => $quantity,         // How many
        'customization' => ''            // Custom instructions (empty for now)
    ]);
}
// =============================================================================
// END SECTION: Cart Update Logic
// =============================================================================

// =============================================================================
// SECTION: Cart Count Calculation
// DESCRIPTION: Calculate total items in cart for response
// =============================================================================

// Get all cart items for this user
// FUNCTION: $db->select() - Gets all records matching conditions
$cartItems = $db->select('cafe_cart', ['user_id' => $user_id]);

// Calculate total quantity (sum of all item quantities)
$cart_count = 0;
foreach ($cartItems as $cartItem) {
    $cart_count += $cartItem['quantity'];
}
// =============================================================================
// END SECTION: Cart Count Calculation
// =============================================================================

// =============================================================================
// SECTION: Success Response
// DESCRIPTION: Return JSON success response with updated cart info
// =============================================================================

// Return success response
// This JSON is parsed by JavaScript to show toast notification
echo json_encode([
    'success' => true,
    'message' => 'Added to cart successfully!',
    'cart_count' => $cart_count,      // Total items (for badge update)
    'item_name' => $item['name']      // Item name (for message)
]);
// =============================================================================
// END SECTION: Success Response
// =============================================================================
?>
