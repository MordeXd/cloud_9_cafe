<?php
/**
 * =============================================================================
 * CLOUD 9 CAFE - LOGIN PAGE
 * =============================================================================
 * 
 * ROLE: Handles user and admin authentication. Displays login form and
 *       processes login attempts. Uses cookie-based authentication via TokenAuth.
 * 
 * FLOW: 1. Includes database config
 *       2. Checks if login form was submitted (POST request)
 *       3. Validates credentials against database
 *       4. If admin → sets admin cookie → redirects to admin dashboard
 *       5. If user → sets user cookie → redirects to user dashboard
 *       6. If invalid → shows error message
 *       7. Displays login form
 * 
 * SECURITY: Passwords are compared in plain text (demo purposes).
 *           In production, use password_hash() and password_verify()
 */

// =============================================================================
// SECTION: Database Configuration Include (MySQL only)
// =============================================================================
include_once '../config/db.php'; // sets $con
include_once '../config/TokenAuth.php';
$auth = new TokenAuth();

// Start PHP session so legacy guards (requireUser/requireAdmin) work after login
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// =============================================================================
// END SECTION: Database Configuration Include
// =============================================================================

// =============================================================================
// SECTION: Login Form Processing
// DESCRIPTION: Process login form submission when user clicks "Login" button
// =============================================================================

// If already logged in via token, skip login page
if ($auth->isAdminLoggedIn()) {
    // Sync legacy session flags
    $_SESSION['admin'] = true;
    $_SESSION['user'] = null;
    $_SESSION['user_id'] = $auth->getAdminId();
    header("Location: /cloud_9_cafe/admin/dashboard.php");
    exit();
}
if ($auth->isUserLoggedIn()) {
    // Sync legacy session flags
    $_SESSION['user'] = true;
    $_SESSION['admin'] = null;
    $_SESSION['user_id'] = $auth->getUserId();
    header("Location: /cloud_9_cafe/user/dashboard.php");
    exit();
}

// Check if login button was clicked (form submitted via POST)
// FUNCTION: isset() - Checks if variable exists and is not NULL
if (isset($_POST['login_btn'])) {
    
    // Get form data and sanitize
    // FUNCTION: $_POST[] - Superglobal array for HTTP POST data
    $email = $_POST['email'];      // User's email address
    $password = $_POST['password']; // User's password (plain text in demo)
    
    // -------------------------------------------------------------------------
    // STEP 1: Fetch user/admin from MySQL users table
    // -------------------------------------------------------------------------
    $stmt = mysqli_prepare($con, "SELECT * FROM users WHERE email=? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $row = $res ? mysqli_fetch_assoc($res) : null;
    mysqli_stmt_close($stmt);

    if ($row) {
        $fullName = $row['full_name'] ?? $row['fullname'] ?? $row['name'] ?? '';
        $role     = strtolower($row['role'] ?? 'user');
        $hash     = $row['password'] ?? '';
        $status   = strtolower($row['status'] ?? 'active');

        $passOk = password_verify($password, $hash) || hash_equals($hash, $password);
        if ($passOk && $status === 'active') {
            // Set session + token for compatibility
            $_SESSION['user_id'] = (int)$row['id'];
            if ($role === 'admin') {
                $_SESSION['admin'] = true;
                $_SESSION['user'] = null;
                $auth->loginAdmin($row['id'], $fullName ?: 'Admin', 'admin');
                header("Location: /cloud_9_cafe/admin/dashboard.php");
                exit();
            } else {
                $_SESSION['user'] = true;
                $_SESSION['admin'] = null;
                $auth->loginUser($row['id'], $fullName ?: 'User', 'User');
                header("Location: /cloud_9_cafe/user/dashboard.php");
                exit();
            }
        }
    }

    // No match found - Invalid credentials
    $login_error = "Invalid email or password";
}
// =============================================================================
// END SECTION: Login Form Processing
// =============================================================================

// =============================================================================
// SECTION: Page Title Setup
// DESCRIPTION: Set page title for browser tab
// =============================================================================
$title = "Login - Cloud 9 Cafe";

// Start output buffering to capture content for layout
ob_start();
?>

<!-- ========================================================================= -->
<!-- SECTION: Login Form Container -->
<!-- DESCRIPTION: Card with login form containing email, password fields -->
<!-- ========================================================================= -->
<div class="container py-5">
    <div class="row justify-content-center align-items-center min-vh-75">
        <div class="col-lg-5 col-md-6">
            
            <!-- Login Card -->
            <div class="card border-0 shadow-lg animate-fade-in-up">
                <div class="card-body p-4 p-md-5">
                    
                    <!-- Header with Logo -->
                    <div class="text-center mb-4">
                        <div class="mb-3">
                            <div class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 80px; height: 80px; background: var(--gradient-primary);">
                                <i class="fas fa-mug-hot fa-2x text-white"></i>
                            </div>
                        </div>
                        <h2 class="fw-bold text-primary mb-2">Welcome Back!</h2>
                        <p class="text-muted mb-0">Sign in to your Cloud 9 account</p>
                    </div>
                    
                    <!-- Error Message Display - Shows only if $login_error exists -->
                    <?php if (isset($login_error)): ?>
                    <div class="alert alert-danger d-flex align-items-center" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <?php echo $login_error; ?>
                    </div>
                    <?php endif; ?>

                    <!-- Login Form -->
                    <!-- FORM ACTION: Submits to this same file (login.php) via POST method -->
                    <form action="login.php" method="POST" id="loginForm">
                        
                        <!-- Email Field -->
                        <div class="mb-4">
                            <label for="email" class="form-label fw-medium">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fas fa-envelope text-muted"></i>
                                </span>
                                <!-- data-validation: Used by validate.js for client-side validation -->
                                <input type="email" class="form-control border-start-0 ps-0" id="email" name="email" 
                                       placeholder="Enter your email" 
                                       data-validation="required,email">
                            </div>
                            <!-- Error display container for email field -->
                            <div id="email_error" class="invalid-feedback d-block"></div>
                        </div>

                        <!-- Password Field -->
                        <div class="mb-4">
                            <label for="password" class="form-label fw-medium">Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                <i class="fas fa-lock text-muted"></i>
                                </span>
                                <input type="password" class="form-control border-start-0 ps-0" id="password" name="password" 
                                       placeholder="Enter your password" 
                                       data-validation="required,min" data-min="6">
                                <!-- Toggle Password Visibility Button -->
                                <button type="button" class="btn btn-outline-secondary border-start-0 toggle-password" data-target="#password">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div id="password_error" class="invalid-feedback d-block"></div>
                        </div>

                        <!-- Remember Me & Forgot Password Row -->
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="remember" name="remember">
                                <label class="form-check-label text-muted small" for="remember">
                                    Remember me
                                </label>
                            </div>
                            <!-- Forgot Password Link - Click redirects to: forgot_password.php -->
                            <a href="forgot_password.php" class="text-primary small text-decoration-none">Forgot Password?</a>
                        </div>

                        <!-- Login Button -->
                        <!-- On click: Submits form with name="login_btn" -->
                        <button type="submit" name="login_btn" class="btn btn-primary w-100 mb-3">
                            <i class="fas fa-sign-in-alt me-2"></i>Sign In
                        </button>

                        <!-- Divider -->
                        <div class="text-center mb-3">
                            <span class="text-muted small">or</span>
                        </div>

                        <!-- Register Link -->
                        <div class="text-center">
                            <span class="text-muted small">Don't have an account?</span>
                            <!-- Register Link - Click redirects to: register.php -->
                            <a href="register.php" class="text-primary fw-semibold small text-decoration-none ms-1">Create Account</a>
                        </div>
                    </form>
                    <!-- END: Login Form -->

                </div>
            </div>
            
            <!-- Back to Home Link -->
            <div class="text-center mt-4">
                <!-- Click redirects to: ../pages/index.php (Homepage) -->
                <a href="../pages/index.php" class="text-muted text-decoration-none small">
                    <i class="fas fa-arrow-left me-1"></i>Back to Home
                </a>
            </div>
            
        </div>
    </div>
</div>
<!-- ========================================================================= -->
<!-- END SECTION: Login Form Container -->
<!-- ========================================================================= -->

<?php
// =============================================================================
// SECTION: Include Layout Wrapper
// DESCRIPTION: Capture output buffer and include the main layout file
// =============================================================================
$content = ob_get_clean();
include '../includes/layout.php';
// =============================================================================
// END SECTION: Include Layout Wrapper
// =============================================================================
?>
