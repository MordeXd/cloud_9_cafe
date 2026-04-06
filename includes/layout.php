<?php
/**
 * =============================================================================
 * CLOUD 9 CAFE - PUBLIC LAYOUT FILE
 * =============================================================================
 * 
 * ROLE: This is the main layout wrapper for all PUBLIC pages of the website.
 *       It provides the common HTML structure including navbar, footer, and
 *       shared CSS/JS resources. Content pages include this file and pass
 *       their content via the $content variable.
 * 
 * USED BY: All pages in /pages/ folder and /auth/ folder
 * 
 * FLOW: 1. Includes database config
 *       2. Fetches user data if logged in
 *       3. Outputs HTML with navbar and footer
 *       4. Injects page content via $content variable
 */

// =============================================================================
// SECTION: Database & Authentication Setup
// DESCRIPTION: Includes the database configuration file which initializes
//              the JsonDB connection and TokenAuth system for cookie-based auth
// =============================================================================
include_once(__DIR__ . "/../config/db_config.php");

// =============================================================================
// SECTION: User Session Data Fetching
// DESCRIPTION: If user is logged in via cookie auth, fetch their profile data
//              and calculate their cart item count for display in navbar
// =============================================================================
$current_user_data = null;  // Stores logged-in user's profile data
$cart_count = 0;            // Stores total items in user's cart
$display_name = 'User';     // Display name fallback

// Check if user is logged in using cookie-based auth
if ($auth->isUserLoggedIn()) {
    $uid = $auth->getUserId();  // Get user ID from auth cookie
    
    // Fetch user profile from database
    $current_user_data = $db->selectOne('cafe_users', ['id' => $uid]);
    if (!empty($current_user_data['fullname'])) {
        $display_name = $current_user_data['fullname'];
    } else {
        // Fallback to token payload name if profile missing
        $display_name = $auth->getUserName() ?: 'User';
    }
    
    // Get cart item count for navbar badge
    $cartItems = $db->select('cafe_cart', ['user_id' => $uid]);
    foreach ($cartItems as $item) {
        $cart_count += $item['quantity'];
    }
}
// =============================================================================
// END SECTION: User Session Data Fetching
// =============================================================================
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Dynamic page title - set by each page via $title variable -->
    <title><?php echo isset($title) ? $title : 'Cloud 9 Cafe'; ?></title>
    
    <!-- Google Fonts - Poppins font family for modern look -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS - Frontend framework for responsive design -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- FontAwesome - Icon library for UI icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <!-- Theme CSS - Global theme variables and base styles (colors, spacing, etc.) -->
    <link rel="stylesheet" href="/cloud_9_cafe/assets/css/theme.css">
    
    <!-- Layout CSS - Public layout specific styles (navbar, footer, components) -->
    <link rel="stylesheet" href="/cloud_9_cafe/assets/css/layout/layout.css">
</head>
<body>

    <!-- ========================================================================= -->
    <!-- SECTION: Navigation Bar (Navbar) -->
    <!-- DESCRIPTION: Fixed top navigation with logo, menu links, and user actions -->
    <!--              Shows different options based on login status -->
    <!-- ========================================================================= -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            
            <!-- Logo - Click redirects to: /cloud_9_cafe/pages/index.php (Homepage) -->
            <a class="navbar-brand" href="/cloud_9_cafe/pages/index.php">
                <i class="fas fa-mug-hot"></i>
                Cloud 9 Cafe
            </a>
            
            <!-- Mobile Menu Toggle Button - Shows on screens smaller than LG breakpoint -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
                <i class="fas fa-bars text-primary"></i>
            </button>
            
            <!-- Navbar Links Container - Collapses on mobile -->
            <div class="collapse navbar-collapse" id="mainNav">
                
                <!-- Center Navigation Links -->
                <ul class="navbar-nav mx-auto align-items-center">
                    
                    <!-- Home Link - Click redirects to: index.php -->
                    <!-- Active state: Highlights when user is on index.php -->
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" 
                           href="/cloud_9_cafe/pages/index.php">
                            <i class="fas fa-home me-1 d-lg-none"></i>Home
                        </a>
                    </li>
                    
                    <!-- Menu Link - Click redirects to: menu.php -->
                    <!-- Active state: Highlights when URL contains 'menu' -->
                    <li class="nav-item">
                        <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'menu') !== false ? 'active' : ''; ?>" 
                           href="/cloud_9_cafe/menu.php">
                            <i class="fas fa-coffee me-1 d-lg-none"></i>Menu
                        </a>
                    </li>
                    
                    <!-- About Link - Click redirects to: about.php -->
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'about.php' ? 'active' : ''; ?>" 
                           href="/cloud_9_cafe/pages/about.php">
                            <i class="fas fa-info-circle me-1 d-lg-none"></i>About
                        </a>
                    </li>
                    
                    <!-- Contact Link - Click redirects to: contact.php -->
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'contact.php' ? 'active' : ''; ?>" 
                           href="/cloud_9_cafe/pages/contact.php">
                            <i class="fas fa-envelope me-1 d-lg-none"></i>Contact
                        </a>
                    </li>
                </ul>
                
                <!-- Right Side: User Actions (Cart, Profile or Login/Register) -->
                <ul class="navbar-nav align-items-center">
                    <?php 
                    // =====================================================================
                    // CONDITION: Check if user is logged in
                    // If YES: Show Cart icon and User dropdown menu
                    // If NO:  Show Login and Register buttons
                    // =====================================================================
                    if ($auth->isUserLoggedIn()): 
                    ?>
                        <!-- Cart Icon with Badge - Click redirects to: user/cart.php -->
                        <li class="nav-item me-3">
                            <a class="nav-link position-relative" href="/cloud_9_cafe/user/cart.php">
                                <i class="fas fa-shopping-cart"></i>
                                <!-- Cart count badge - Updates dynamically via JavaScript -->
                                <span class="cart-badge" id="navbarCartCount"><?php echo $cart_count; ?></span>
                            </a>
                        </li>
                        
                        <!-- User Dropdown Menu - Shows profile, orders, logout -->
                        <li class="nav-item dropdown">
                            <!-- Toggle dropdown on click -->
                            <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" data-bs-toggle="dropdown">
                                <!-- User avatar placeholder (no profile image load to reduce requests) -->
                                <span class="nav-avatar d-inline-flex align-items-center justify-content-center bg-primary text-white">
                                    <i class="fas fa-user"></i>
                                </span>
                                <span class="fw-medium"><?php echo htmlspecialchars($display_name); ?></span>
                            </a>
                            <!-- Dropdown menu items -->
                            <ul class="dropdown-menu dropdown-menu-end">
                                <!-- Dashboard - Click redirects to: user/dashboard.php -->
                                <li><a class="dropdown-item" href="/cloud_9_cafe/user/dashboard.php"><i class="fas fa-th-large me-2 text-primary"></i>Dashboard</a></li>
                                <!-- Profile - Click redirects to: user/profile.php -->
                                <li><a class="dropdown-item" href="/cloud_9_cafe/user/profile.php"><i class="fas fa-user me-2 text-primary"></i>Profile</a></li>
                                <!-- Orders - Click redirects to: user/orders.php -->
                                <li><a class="dropdown-item" href="/cloud_9_cafe/user/orders.php"><i class="fas fa-shopping-bag me-2 text-primary"></i>My Orders</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <!-- Logout - Click redirects to: auth/logout.php (clears auth cookie) -->
                                <li><a class="dropdown-item text-danger" href="/cloud_9_cafe/auth/logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <!-- Not Logged In: Show Login and Register buttons -->
                        <!-- Login Button - Click redirects to: auth/login.php -->
                        <li class="nav-item">
                            <a class="btn btn-outline-primary me-2" href="/cloud_9_cafe/auth/login.php">Login</a>
                        </li>
                        <!-- Register Button - Click redirects to: auth/register.php -->
                        <li class="nav-item">
                            <a class="btn btn-primary" href="/cloud_9_cafe/auth/register.php">Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
                <!-- END: User Actions -->
            </div>
        </div>
    </nav>
    <!-- ========================================================================= -->
    <!-- END SECTION: Navigation Bar (Navbar) -->
    <!-- ========================================================================= -->

    <!-- Spacer for fixed navbar - Prevents content from being hidden under navbar -->
    <div style="height: var(--navbar-height);"></div>

    <!-- ========================================================================= -->
    <!-- SECTION: Main Content Area -->
    <!-- DESCRIPTION: This is where page-specific content is injected -->
    <!--              Each page sets $content variable before including this layout -->
    <!-- ========================================================================= -->
    <main>
        <?php 
        // Echo the page content passed from the including file
        echo $content; 
        ?>
    </main>
    <!-- ========================================================================= -->
    <!-- END SECTION: Main Content Area -->
    <!-- ========================================================================= -->

    <!-- ========================================================================= -->
    <!-- SECTION: Footer -->
    <!-- DESCRIPTION: Site footer with logo, quick links, support links, and contact info -->
    <!-- ========================================================================= -->
    <footer class="main-footer">
        <div class="container">
            <div class="row g-4">
                
                <!-- Column 1: Brand Info & Social Links -->
                <div class="col-lg-4">
                    <!-- Footer Logo - Click redirects to: index.php (Homepage) -->
                    <a href="/cloud_9_cafe/pages/index.php" class="footer-brand text-decoration-none">
                        <i class="fas fa-mug-hot me-2"></i>Cloud 9 Cafe
                    </a>
                    <p class="mt-3 mb-4">Your perfect destination for premium coffee, delicious snacks, and delightful desserts. Experience the taste of perfection.</p>
                    <!-- Social Media Links -->
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                
                <!-- Column 2: Quick Links -->
                <div class="col-lg-2 col-md-4">
                    <h5 class="footer-title">Quick Links</h5>
                    <ul class="list-unstyled">
                        <!-- Home - Click redirects to: index.php -->
                        <li class="mb-2"><a href="/cloud_9_cafe/pages/index.php">Home</a></li>
                        <!-- Menu - Click redirects to: menu.php -->
                        <li class="mb-2"><a href="/cloud_9_cafe/menu.php">Menu</a></li>
                        <!-- About Us - Click redirects to: about.php -->
                        <li class="mb-2"><a href="/cloud_9_cafe/pages/about.php">About Us</a></li>
                    </ul>
                </div>
                
                <!-- Column 3: Support Links -->
                <div class="col-lg-2 col-md-4">
                    <h5 class="footer-title">Support</h5>
                    <ul class="list-unstyled">
                        <!-- Contact - Click redirects to: contact.php -->
                        <li class="mb-2"><a href="/cloud_9_cafe/pages/contact.php">Contact</a></li>
                        <!-- FAQ - Click redirects to: faq.php -->
                        <li class="mb-2"><a href="/cloud_9_cafe/pages/faq.php">FAQ</a></li>
                        <!-- Privacy Policy - Click redirects to: privacy_policy.php -->
                        <li class="mb-2"><a href="/cloud_9_cafe/pages/privacy_policy.php">Privacy Policy</a></li>
                        <!-- Terms - Click redirects to: terms_of_service.php -->
                        <li class="mb-2"><a href="/cloud_9_cafe/pages/terms_of_service.php">Terms</a></li>
                    </ul>
                </div>
                
                <!-- Column 4: Contact Information -->
                <div class="col-lg-4 col-md-4">
                    <h5 class="footer-title">Contact Us</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="fas fa-map-marker-alt me-2 text-accent"></i>123 Coffee Street, Cafe City</li>
                        <li class="mb-2"><i class="fas fa-phone me-2 text-accent"></i>+1 (555) 123-4567</li>
                        <li class="mb-2"><i class="fas fa-envelope me-2 text-accent"></i>hello@cloud9cafe.com</li>
                        <li><i class="fas fa-clock me-2 text-accent"></i>Open Daily: 7AM - 10PM</li>
                    </ul>
                </div>
            </div>
            
            <!-- Footer Bottom: Copyright -->
            <hr class="my-4" style="border-color: rgba(255,255,255,0.1);">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start">
                    <p class="mb-0">&copy; <?php echo date('Y'); ?> Cloud 9 Cafe. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <p class="mb-0">Made with <i class="fas fa-heart text-danger"></i> for coffee lovers</p>
                </div>
            </div>
        </div>
    </footer>
    <!-- ========================================================================= -->
    <!-- END SECTION: Footer -->
    <!-- ========================================================================= -->

    <!-- Toast Container - For displaying notification messages -->
    <div class="toast-container" id="toastContainer"></div>

    <!-- Bootstrap JS - Required for navbar toggle, dropdowns, modals -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Theme JS - Custom JavaScript for theme functionality -->
    <script src="/cloud_9_cafe/assets/js/theme.js"></script>
</body>
</html>
