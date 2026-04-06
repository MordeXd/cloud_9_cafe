<?php
/**
 * =============================================================================
 * CLOUD 9 CAFE - TOKEN-BASED AUTHENTICATION CLASS
 * =============================================================================
 * 
 * ROLE: This class provides cookie-based authentication using signed tokens.
 *       It replaces PHP sessions with stateless cookie authentication.
 * 
 * FEATURES:
 * - Signed tokens using HMAC-SHA256 for security
 * - 30-day cookie expiry
 * - HTTP-only cookies (prevents XSS access)
 * - SameSite=Lax for CSRF protection
 * - Supports both user and admin authentication
 * 
 * TOKEN STRUCTURE: base64(payload).signature
 *   - payload: JSON with type, id, name, role, exp (expiry)
 *   - signature: HMAC-SHA256 of payload
 * 
 * USAGE:
 *   $auth = new TokenAuth();
 *   $auth->loginUser($userId, $userName, $role);
 *   if ($auth->isUserLoggedIn()) { ... }
 */

class TokenAuth {
    
    // =============================================================================
    // SECTION: Class Properties
    // DESCRIPTION: Configuration variables for the authentication system
    // =============================================================================
    
    private $secretKey;           // Secret key for signing tokens (kept private)
    private $cookieName = 'auth_token';  // Name of the authentication cookie
    private $cookieExpiry = 86400 * 30;  // Cookie expiry time: 30 days (in seconds)
    
    // =============================================================================
    // END SECTION: Class Properties
    // =============================================================================
    
    // =============================================================================
    // SECTION: Constructor
    // DESCRIPTION: Initialize the authentication class with secret key
    // =============================================================================
    
    /**
     * Constructor - Initializes the TokenAuth instance
     * Sets up the secret key used for signing tokens
     */
    public function __construct() {
        // Generate secret key based on current month
        // In production, use a strong random secret stored securely
        $this->secretKey = 'cloud9_cafe_secret_key_' . date('Y-m');
    }
    // =============================================================================
    // END SECTION: Constructor
    // =============================================================================
    
    // =============================================================================
    // SECTION: Token Generation (Private)
    // DESCRIPTION: Creates signed tokens for authentication
    // =============================================================================
    
    /**
     * Generate a signed token from data array
     * 
     * FUNCTION: generateToken()
     * PARAMETER: $data (array) - Data to encode in token (type, id, name, role, exp)
     * RETURNS: (string) - Signed token in format: base64(payload).signature
     * 
     * PROCESS:
     *   1. Convert data array to JSON
     *   2. Create HMAC-SHA256 signature using secret key
     *   3. Return base64(payload) + '.' + signature
     */
    private function generateToken($data) {
        // Convert data array to JSON string
        $payload = json_encode($data);
        
        // Create signature using HMAC-SHA256
        // FUNCTION: hash_hmac() - Generates keyed hash value
        $signature = hash_hmac('sha256', $payload, $this->secretKey);
        
        // Return token in format: base64(payload).signature
        return base64_encode($payload) . '.' . $signature;
    }
    // =============================================================================
    // END SECTION: Token Generation
    // =============================================================================
    
    // =============================================================================
    // SECTION: Token Verification (Private)
    // DESCRIPTION: Verifies and decodes signed tokens
    // =============================================================================
    
    /**
     * Verify and decode a token
     * 
     * FUNCTION: verifyToken()
     * PARAMETER: $token (string) - The signed token to verify
     * RETURNS: (array|false) - Decoded data if valid, false if invalid
     * 
     * PROCESS:
     *   1. Split token into payload and signature parts
     *   2. Decode base64 payload
     *   3. Recalculate expected signature
     *   4. Compare signatures using hash_equals (timing-safe)
     *   5. Return decoded data if valid
     */
    private function verifyToken($token) {
        // Split token into parts
        $parts = explode('.', $token);
        
        // Validate token format (must have exactly 2 parts)
        if (count($parts) !== 2) return false;
        
        // Decode base64 payload
        $payload = base64_decode($parts[0]);
        $signature = $parts[1];
        
        // Calculate expected signature
        $expectedSignature = hash_hmac('sha256', $payload, $this->secretKey);
        
        // Verify signature using timing-safe comparison
        // FUNCTION: hash_equals() - Prevents timing attacks
        if (!hash_equals($expectedSignature, $signature)) return false;
        
        // Return decoded JSON data as associative array
        return json_decode($payload, true);
    }
    // =============================================================================
    // END SECTION: Token Verification
    // =============================================================================
    
    // =============================================================================
    // SECTION: User Login
    // DESCRIPTION: Sets authentication cookie for regular users
    // =============================================================================
    
    /**
     * Login a user and set authentication cookie
     * 
     * FUNCTION: loginUser()
     * PARAMETERS:
     *   - $userId (int) - User's database ID
     *   - $userName (string) - User's display name
     *   - $role (string) - User's role (default: 'User')
     * RETURNS: (bool) - Always returns true
     * 
     * PROCESS:
     *   1. Create token payload with user data and expiry
     *   2. Generate signed token
     *   3. Set HTTP-only cookie with token
     */
    public function loginUser($userId, $userName, $role = 'User') {
        // Create token data array
        $data = [
            'type' => 'user',                    // Distinguishes from admin tokens
            'id' => $userId,                     // User ID from database
            'name' => $userName,                 // User's display name
            'role' => $role,                     // User role
            'exp' => time() + $this->cookieExpiry  // Expiry timestamp (30 days)
        ];
        
        // Generate signed token
        $token = $this->generateToken($data);
        
        // Set cookie with token
        // FUNCTION: setcookie() - Sends HTTP cookie to browser
        setcookie($this->cookieName, $token, [
            'expires' => time() + $this->cookieExpiry,  // Cookie expiry
            'path' => '/',                              // Available site-wide
            'secure' => false,                          // Set to true in production with HTTPS
            'httponly' => true,                         // Prevents JavaScript access (XSS protection)
            'samesite' => 'Lax'                         // CSRF protection
        ]);
        
        return true;
    }
    // =============================================================================
    // END SECTION: User Login
    // =============================================================================
    
    // =============================================================================
    // SECTION: Admin Login
    // DESCRIPTION: Sets authentication cookie for admin users
    // =============================================================================
    
    /**
     * Login an admin and set authentication cookie
     * 
     * FUNCTION: loginAdmin()
     * PARAMETERS:
     *   - $adminId (int) - Admin's database ID
     *   - $adminName (string) - Admin's display name
     *   - $role (string) - Admin's role (super_admin, manager, staff)
     * RETURNS: (bool) - Always returns true
     */
    public function loginAdmin($adminId, $adminName, $role) {
        $data = [
            'type' => 'admin',                   // Distinguishes from user tokens
            'id' => $adminId,                    // Admin ID from database
            'name' => $adminName,                // Admin's display name
            'role' => $role,                     // Admin role
            'exp' => time() + $this->cookieExpiry
        ];
        
        $token = $this->generateToken($data);
        
        setcookie($this->cookieName, $token, [
            'expires' => time() + $this->cookieExpiry,
            'path' => '/',
            'secure' => false,
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
        
        return true;
    }
    // =============================================================================
    // END SECTION: Admin Login
    // =============================================================================
    
    // =============================================================================
    // SECTION: Logout
    // DESCRIPTION: Clears the authentication cookie
    // =============================================================================
    
    /**
     * Logout user/admin by clearing the auth cookie
     * 
     * FUNCTION: logout()
     * RETURNS: (bool) - Always returns true
     * 
     * PROCESS: Sets cookie with past expiry time to delete it
     */
    public function logout() {
        // Set cookie with past expiry to delete it
        setcookie($this->cookieName, '', [
            'expires' => time() - 3600,  // 1 hour ago (past)
            'path' => '/',
            'secure' => false,
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
        return true;
    }
    // =============================================================================
    // END SECTION: Logout
    // =============================================================================
    
    // =============================================================================
    // SECTION: Get Current User Data
    // DESCRIPTION: Retrieves and validates current user/admin from cookie
    // =============================================================================
    
    /**
     * Get current user/admin data from cookie
     * 
     * FUNCTION: getCurrentUser()
     * RETURNS: (array|null) - Decoded token data if valid, null otherwise
     * 
     * PROCESS:
     *   1. Check if auth cookie exists
     *   2. Verify token signature
     *   3. Check if token is expired
     *   4. Return data if valid, logout if expired
     */
    public function getCurrentUser() {
        // Check if cookie exists
        if (!isset($_COOKIE[$this->cookieName])) return null;
        
        // Verify token
        $data = $this->verifyToken($_COOKIE[$this->cookieName]);
        if (!$data) return null;
        
        // Check expiration
        if ($data['exp'] < time()) {
            // Token expired - clear cookie
            $this->logout();
            return null;
        }
        
        return $data;
    }
    // =============================================================================
    // END SECTION: Get Current User Data
    // =============================================================================
    
    // =============================================================================
    // SECTION: Authentication Checks
    // DESCRIPTION: Check if user or admin is logged in
    // =============================================================================
    
    /**
     * Check if a user (not admin) is logged in
     * 
     * FUNCTION: isUserLoggedIn()
     * RETURNS: (bool) - True if valid user token exists
     */
    public function isUserLoggedIn() {
        $user = $this->getCurrentUser();
        return $user && $user['type'] === 'user';
    }
    
    /**
     * Check if an admin is logged in
     * 
     * FUNCTION: isAdminLoggedIn()
     * RETURNS: (bool) - True if valid admin token exists
     */
    public function isAdminLoggedIn() {
        $user = $this->getCurrentUser();
        return $user && $user['type'] === 'admin';
    }
    // =============================================================================
    // END SECTION: Authentication Checks
    // =============================================================================
    
    // =============================================================================
    // SECTION: Data Retrieval
    // DESCRIPTION: Get specific data from authenticated user
    // =============================================================================
    
    /**
     * Get logged-in user's ID
     * FUNCTION: getUserId()
     * RETURNS: (int|null) - User ID if logged in, null otherwise
     */
    public function getUserId() {
        $user = $this->getCurrentUser();
        return $user ? $user['id'] : null;
    }
    
    /**
     * Get logged-in admin's ID
     * FUNCTION: getAdminId()
     * RETURNS: (int|null) - Admin ID if logged in, null otherwise
     */
    public function getAdminId() {
        $user = $this->getCurrentUser();
        return ($user && $user['type'] === 'admin') ? $user['id'] : null;
    }
    
    /**
     * Get logged-in user's name
     * FUNCTION: getUserName()
     * RETURNS: (string|null) - User name if logged in, null otherwise
     */
    public function getUserName() {
        $user = $this->getCurrentUser();
        return $user ? $user['name'] : null;
    }
    
    /**
     * Get logged-in admin's role
     * FUNCTION: getAdminRole()
     * RETURNS: (string|null) - Admin role if logged in, null otherwise
     */
    public function getAdminRole() {
        $user = $this->getCurrentUser();
        return ($user && $user['type'] === 'admin') ? $user['role'] : null;
    }
    // =============================================================================
    // END SECTION: Data Retrieval
    // =============================================================================
    
    // =============================================================================
    // SECTION: Required Login Guards
    // DESCRIPTION: Redirect to login if not authenticated
    // =============================================================================
    
    /**
     * Require user login - redirect if not logged in
     * 
     * FUNCTION: requireUser()
     * ACTION: Redirects to ../auth/login.php if user is not logged in
     */
    public function requireUser() {
        if (!$this->isUserLoggedIn()) {
            header("Location: ../auth/login.php");
            exit();
        }
    }
    
    /**
     * Require admin login - redirect if not logged in
     * 
     * FUNCTION: requireAdmin()
     * ACTION: Redirects to ../auth/login.php if admin is not logged in
     */
    public function requireAdmin() {
        if (!$this->isAdminLoggedIn()) {
            header("Location: ../auth/login.php");
            exit();
        }
    }
    // =============================================================================
    // END SECTION: Required Login Guards
    // =============================================================================
}

// =============================================================================
// SECTION: Global Instance Creation
// DESCRIPTION: Create global $auth instance for use across the application
// =============================================================================
$auth = new TokenAuth();
// =============================================================================
// END SECTION: Global Instance Creation
// =============================================================================

// =============================================================================
// SECTION: Helper Functions
// DESCRIPTION: Global helper functions for backward compatibility
// =============================================================================

/**
 * Check if any user is logged in
 * FUNCTION: isLoggedIn()
 * RETURNS: (bool) - True if user is logged in
 */
function isLoggedIn() {
    global $auth;
    return $auth->isUserLoggedIn();
}

/**
 * Get current user ID
 * FUNCTION: getCurrentUserId()
 * RETURNS: (int|null) - User ID if logged in
 */
function getCurrentUserId() {
    global $auth;
    return $auth->getUserId();
}

/**
 * Get current user name
 * FUNCTION: getCurrentUserName()
 * RETURNS: (string|null) - User name if logged in
 */
function getCurrentUserName() {
    global $auth;
    return $auth->getUserName();
}

/**
 * Require user login
 * FUNCTION: requireLogin()
 * ACTION: Redirects to login if not logged in
 */
function requireLogin() {
    global $auth;
    $auth->requireUser();
}
// =============================================================================
// END SECTION: Helper Functions
// =============================================================================
?>
