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
// SECTION: Database & Authentication Include (MySQL)
// =============================================================================
require_once '../config/db.php';
require_once '../config/TokenAuth.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$auth = new TokenAuth();
$tokenUser = $auth->getCurrentUser();
$isAdmin = ($tokenUser && (($tokenUser['type'] ?? '') === 'admin')) || !empty($_SESSION['admin']);

if ($isAdmin) {
    echo json_encode([
        'success' => false,
        'admin_only' => true,
        'message' => 'Admins cannot place orders. Please login as a customer.'
    ]);
    exit();
}
// =============================================================================
// END SECTION: Database & Authentication Include
// =============================================================================

// =============================================================================
// SECTION: Authentication Check
// DESCRIPTION: Verify user is logged in, return redirect if not
// =============================================================================

// Check if user is logged in (session or token)
$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id && $auth->isUserLoggedIn()) {
    $user_id = $auth->getUserId();
    $_SESSION['user_id'] = $user_id;
}
if (!$user_id) {
    
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

// Fetch menu item from MySQL
$stmt = mysqli_prepare($con, "SELECT * FROM menu_items WHERE id = ? AND status = 'active' LIMIT 1");
mysqli_stmt_bind_param($stmt, "i", $menu_item_id);
mysqli_stmt_execute($stmt);
$itemRes = mysqli_stmt_get_result($stmt);
$item = $itemRes ? mysqli_fetch_assoc($itemRes) : null;
mysqli_stmt_close($stmt);

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
$existingRes = mysqli_query($con, "SELECT id, quantity FROM cart WHERE user_id = $user_id AND menu_item_id = $menu_item_id LIMIT 1");
$existing = $existingRes ? mysqli_fetch_assoc($existingRes) : null;

if ($existing) {
    $newQty = $existing['quantity'] + $quantity;
    mysqli_query($con, "UPDATE cart SET quantity = $newQty WHERE id = {$existing['id']}");
} else {
    mysqli_query($con, "INSERT INTO cart (user_id, menu_item_id, quantity) VALUES ($user_id, $menu_item_id, $quantity)");
}
// =============================================================================
// END SECTION: Cart Update Logic
// =============================================================================

// =============================================================================
// SECTION: Cart Count Calculation
// DESCRIPTION: Calculate total items in cart for response
// =============================================================================

// Get total cart quantity
$cartRes = mysqli_query($con, "SELECT COALESCE(SUM(quantity),0) AS qty FROM cart WHERE user_id = $user_id");
$cartRow = $cartRes ? mysqli_fetch_assoc($cartRes) : ['qty' => 0];
$cart_count = (int)$cartRow['qty'];
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
    'item_name' => $item['item_name'] // Item name (for message)
]);
// =============================================================================
// END SECTION: Success Response
// =============================================================================
?>
