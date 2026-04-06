<?php
include_once '../config/db.php';
include_once '../config/TokenAuth.php';
$auth = new TokenAuth();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$title = "Register - Cloud 9 Cafe";

// Registration processing
if (isset($_POST['reg_btn'])) {
    $fname = $_POST['firstName'];
    $lname = $_POST['lastName'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];
    $gender = $_POST['gender'];
    $address = "Rajkot";
    
    $fullname = $fname . " " . $lname;
    $token = uniqid();
    
    // Check if email already exists
    $emailSafe = mysqli_real_escape_string($con, $email);
    $check = mysqli_query($con, "SELECT id FROM users WHERE email = '$emailSafe' LIMIT 1");
    if ($check && mysqli_num_rows($check) > 0) {
        $register_error = "Email already registered. Please use a different email.";
    } else {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = mysqli_prepare($con, "INSERT INTO users (full_name, email, password, role, status, mobile) VALUES (?, ?, ?, 'user', 'active', ?)");
        mysqli_stmt_bind_param($stmt, "ssss", $fullname, $email, $hash, $phone);
        $ok = mysqli_stmt_execute($stmt);
        $newUserId = $ok ? mysqli_insert_id($con) : null;
        mysqli_stmt_close($stmt);

        if ($newUserId) {
            $_SESSION['user'] = true;
            $_SESSION['user_id'] = $newUserId;
            $auth->loginUser($newUserId, $fullname, 'User');
            header("Location: ../user/dashboard.php");
            exit();
        } else {
            $register_error = "Error in registration";
        }
    }
}

ob_start();
?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <!-- Register Card -->
            <div class="card border-0 shadow-lg animate-fade-in-up">
                <div class="card-body p-4 p-md-5">
                    <!-- Header -->
                    <div class="text-center mb-4">
                        <div class="mb-3">
                            <div class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 80px; height: 80px; background: var(--gradient-primary);">
                                <i class="fas fa-user-plus fa-2x text-white"></i>
                            </div>
                        </div>
                        <h2 class="fw-bold text-primary mb-2">Create Account</h2>
                        <p class="text-muted mb-0">Join Cloud 9 Cafe and start your coffee journey</p>
                    </div>

                    <!-- Error Message -->
                    <?php if (isset($register_error)): ?>
                    <div class="alert alert-danger d-flex align-items-center mb-4" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <?php echo $register_error; ?>
                    </div>
                    <?php endif; ?>

                    <!-- Registration Form -->
                    <form action="register.php" method="post" id="regform">
                        <div class="row g-3">
                            <!-- First Name -->
                            <div class="col-md-6">
                                <label for="firstName" class="form-label fw-medium">First Name</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fas fa-user text-muted"></i>
                                    </span>
                                    <input type="text" class="form-control border-start-0 ps-0" id="firstName" name="firstName" 
                                           placeholder="John" 
                                           data-validation="required,alphabetic,min" data-min="2">
                                </div>
                                <div id="firstName_error" class="invalid-feedback d-block"></div>
                            </div>

                            <!-- Last Name -->
                            <div class="col-md-6">
                                <label for="lastName" class="form-label fw-medium">Last Name</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fas fa-user text-muted"></i>
                                    </span>
                                    <input type="text" class="form-control border-start-0 ps-0" id="lastName" name="lastName" 
                                           placeholder="Doe" 
                                           data-validation="required,alphabetic,min" data-min="2">
                                </div>
                                <div id="lastName_error" class="invalid-feedback d-block"></div>
                            </div>

                            <!-- Email -->
                            <div class="col-md-6">
                                <label for="email" class="form-label fw-medium">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fas fa-envelope text-muted"></i>
                                    </span>
                                    <input type="email" class="form-control border-start-0 ps-0" id="email" name="email" 
                                           placeholder="john@example.com" 
                                           data-validation="required,email">
                                </div>
                                <div id="email_error" class="invalid-feedback d-block"></div>
                            </div>

                            <!-- Phone -->
                            <div class="col-md-6">
                                <label for="phone" class="form-label fw-medium">Phone Number</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fas fa-phone text-muted"></i>
                                    </span>
                                    <input type="tel" class="form-control border-start-0 ps-0" id="phone" name="phone" 
                                           placeholder="1234567890" 
                                           data-validation="required,number,min" data-min="10" data-max="15">
                                </div>
                                <div id="phone_error" class="invalid-feedback d-block"></div>
                            </div>

                            <!-- Gender -->
                            <div class="col-md-6">
                                <label for="gender" class="form-label fw-medium">Gender</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fas fa-venus-mars text-muted"></i>
                                    </span>
                                    <select class="form-select border-start-0 ps-0" id="gender" name="gender" 
                                            data-validation="required,select">
                                        <option value="">Select Gender</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                                <div id="gender_error" class="invalid-feedback d-block"></div>
                            </div>

                            <!-- Password -->
                            <div class="col-md-6">
                                <label for="password" class="form-label fw-medium">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fas fa-lock text-muted"></i>
                                    </span>
                                    <input type="password" class="form-control border-start-0 ps-0" id="password" name="password" 
                                           placeholder="Create password" 
                                           data-validation="required,strongPassword">
                                    <button type="button" class="btn btn-outline-secondary toggle-password" data-target="#password" tabindex="-1">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <div id="password_error" class="invalid-feedback d-block"></div>
                            </div>

                            <!-- Confirm Password -->
                            <div class="col-md-6">
                                <label for="confirmPassword" class="form-label fw-medium">Confirm Password</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fas fa-lock text-muted"></i>
                                    </span>
                                    <input type="password" class="form-control border-start-0 ps-0" id="confirmPassword" name="confirmPassword" 
                                           placeholder="Confirm password">
                                    <button type="button" class="btn btn-outline-secondary toggle-password" data-target="#confirmPassword" tabindex="-1">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <div id="confirmPassword_error" class="invalid-feedback d-block"></div>
                            </div>
                        </div>

                        <!-- Terms -->
                        <div class="mt-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="terms" name="terms" 
                                       data-validation="required">
                                <label class="form-check-label text-muted small" for="terms">
                                    I agree to the <a href="../pages/terms_of_service.php" class="text-primary">Terms of Service</a> and <a href="../pages/privacy_policy.php" class="text-primary">Privacy Policy</a>
                                </label>
                                <div id="terms_error" class="invalid-feedback d-block"></div>
                            </div>
                        </div>

                        <!-- Submit -->
                        <button type="submit" name="reg_btn" class="btn btn-primary w-100 btn-lg mt-4">
                            <i class="fas fa-user-plus me-2"></i>Create Account
                        </button>

                        <!-- Social Register -->
                        <div class="text-center mt-4">
                            <p class="text-muted small mb-3">Or register with</p>
                            <div class="d-flex gap-2 justify-content-center mb-4">
                                <button type="button" class="btn btn-outline-secondary btn-icon" style="width: 44px; height: 44px;">
                                    <i class="fab fa-google"></i>
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-icon" style="width: 44px; height: 44px;">
                                    <i class="fab fa-facebook-f"></i>
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-icon" style="width: 44px; height: 44px;">
                                    <i class="fab fa-apple"></i>
                                </button>
                            </div>
                            
                            <p class="text-muted mb-0">
                                Already have an account? 
                                <a href="login.php" class="text-primary fw-bold">Sign In</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Back to Home -->
            <div class="text-center mt-4 mb-4">
                <a href="../pages/index.php" class="text-muted text-decoration-none">
                    <i class="fas fa-arrow-left me-2"></i>Back to Home
                </a>
            </div>
        </div>
    </div>
</div>

<style>
    .btn-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0;
    }
    
    .form-check-input:checked {
        background-color: var(--cafe-primary);
        border-color: var(--cafe-primary);
    }
    
    /* Validation styles */
    .is-valid {
        border-color: #198754 !important;
    }
    
    .is-invalid {
        border-color: #dc3545 !important;
    }
    
    /* Toggle password button styling */
    .toggle-password {
        cursor: pointer;
        z-index: 10;
    }
    
    .toggle-password:hover {
        background-color: #e9ecef !important;
    }
</style>

<script src="../assets/js/jquery.js"></script>
<script src="../assets/js/validate.js"></script>
<script>
    // Toggle password visibility - using vanilla JS for better compatibility
    document.addEventListener('DOMContentLoaded', function() {
        var toggleButtons = document.querySelectorAll('.toggle-password');
        
        toggleButtons.forEach(function(button) {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                
                var targetSelector = this.getAttribute('data-target');
                var input = document.querySelector(targetSelector);
                var icon = this.querySelector('i');
                
                if (input && icon) {
                    if (input.getAttribute('type') === 'password') {
                        input.setAttribute('type', 'text');
                        icon.classList.remove('fa-eye');
                        icon.classList.add('fa-eye-slash');
                    } else {
                        input.setAttribute('type', 'password');
                        icon.classList.remove('fa-eye-slash');
                        icon.classList.add('fa-eye');
                    }
                }
            });
        });
        
        // Direct password match validation
        var passwordField = document.getElementById('password');
        var confirmField = document.getElementById('confirmPassword');
        var confirmError = document.getElementById('confirmPassword_error');
        
        function checkPasswordMatch() {
            var pass = passwordField.value;
            var confirm = confirmField.value;
            
            // Always check if confirm has value
            if (confirm.length > 0) {
                // Direct string comparison
                if (pass === confirm) {
                    // MATCH
                    confirmError.textContent = '';
                    confirmError.style.display = 'none';
                    confirmField.classList.remove('is-invalid');
                    confirmField.classList.add('is-valid');
                    return true;
                } else {
                    // NO MATCH
                    confirmError.textContent = 'Passwords do not match.';
                    confirmError.style.display = 'block';
                    confirmField.classList.add('is-invalid');
                    confirmField.classList.remove('is-valid');
                    return false;
                }
            } else {
                // Empty confirm field
                confirmError.textContent = '';
                confirmError.style.display = 'none';
                confirmField.classList.remove('is-invalid', 'is-valid');
                return false;
            }
        }
        
        // Check on input (typing)
        confirmField.addEventListener('input', checkPasswordMatch);
        
        // Check when password changes (if confirm is not empty)
        passwordField.addEventListener('input', function() {
            if (confirmField.value.length > 0) {
                checkPasswordMatch();
            }
        });
        
        // Check on blur (when leaving the field)
        confirmField.addEventListener('blur', checkPasswordMatch);
        
        // Form submit check
        document.getElementById('regform').addEventListener('submit', function(e) {
            var isMatch = checkPasswordMatch();
            if (!isMatch) {
                e.preventDefault();
                confirmField.focus();
            }
        });
    });
</script>

<?php
$content = ob_get_clean();
include '../includes/layout.php';
?>
