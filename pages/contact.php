<?php
require_once '../config/db_config.php';

// Process form submission
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['fname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    
    // Validation
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error = "Please fill in all required fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } elseif (strlen($message) < 10) {
        $error = "Message must be at least 10 characters long.";
    } else {
        $messageData = [
            'name' => $name,
            'email' => $email,
            'subject' => $subject,
            'message' => $message,
            'status' => 'New',
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $db->insert('contact_messages', $messageData);
        $success = "Thank you for your message! We'll get back to you soon.";
        
        // Clear form after successful submission
        $_POST = [];
    }
}

$title = "Contact Us - Cloud 9 Cafe";
ob_start();
?>

<!-- Page Header -->
<section class="py-5" style="background: linear-gradient(135deg, var(--cafe-primary) 0%, var(--cafe-primary-dark) 100%);">
    <div class="container">
        <div class="row justify-content-center text-center text-white">
            <div class="col-lg-8 animate-fade-in-up">
                <h1 class="fw-bold text-white mb-3">Get In Touch</h1>
                <p class="lead opacity-75 text-white mb-0">We'd love to hear from you. Send us a message and we'll respond as soon as possible.</p>
            </div>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section class="py-5" style="background: var(--bg-cream);">
    <div class="container">
        <div class="row g-4">
            <!-- Contact Form -->
            <div class="col-lg-8 animate-on-scroll">
                <div class="card border-0 shadow-lg">
                    <div class="card-body p-4 p-md-5">
                        <h4 class="fw-bold mb-4">
                            <i class="fas fa-paper-plane me-2 text-primary"></i>Send us a Message
                        </h4>
                        
                        <?php if ($success): ?>
                        <div class="alert alert-success d-flex align-items-center" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            <?php echo htmlspecialchars($success); ?>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($error): ?>
                        <div class="alert alert-danger d-flex align-items-center" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                        <?php endif; ?>
                        
                        <form action="" method="POST" id="contactForm" novalidate>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="fname" class="form-label fw-medium">Your Name *</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="fas fa-user text-muted"></i>
                                        </span>
                                        <input type="text" class="form-control border-start-0 ps-0" id="fname" name="fname" 
                                               placeholder="John Doe" 
                                               value="<?php echo htmlspecialchars($_POST['fname'] ?? ''); ?>"
                                               required minlength="2">
                                    </div>
                                    <div class="invalid-feedback">Please enter your name (at least 2 characters).</div>
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label fw-medium">Email Address *</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="fas fa-envelope text-muted"></i>
                                        </span>
                                        <input type="email" class="form-control border-start-0 ps-0" id="email" name="email" 
                                               placeholder="john@example.com" 
                                               value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                                               required>
                                    </div>
                                    <div class="invalid-feedback">Please enter a valid email address.</div>
                                </div>
                            </div>
                            
                            <div class="mt-3">
                                <label for="subject" class="form-label fw-medium">Subject *</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fas fa-tag text-muted"></i>
                                    </span>
                                    <input type="text" class="form-control border-start-0 ps-0" id="subject" name="subject" 
                                           placeholder="How can we help?" 
                                           value="<?php echo htmlspecialchars($_POST['subject'] ?? ''); ?>"
                                           required minlength="3">
                                </div>
                                <div class="invalid-feedback">Please enter a subject (at least 3 characters).</div>
                            </div>
                            
                            <div class="mt-3">
                                <label for="message" class="form-label fw-medium">Message *</label>
                                <textarea class="form-control" id="message" name="message" rows="5" 
                                          placeholder="Tell us more about your inquiry..." 
                                          required minlength="10"><?php echo htmlspecialchars($_POST['message'] ?? ''); ?></textarea>
                                <div class="invalid-feedback">Please enter a message (at least 10 characters).</div>
                                <div class="form-text text-end"><span id="charCount">0</span> characters</div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary btn-lg w-100 mt-4" id="submitBtn">
                                <i class="fas fa-paper-plane me-2"></i>Send Message
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Contact Info -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-lg h-100" style="background: linear-gradient(135deg, var(--cafe-primary) 0%, var(--cafe-primary-dark) 100%);">
                    <div class="card-body p-4 p-md-5 text-white">
                        <h4 class="fw-bold text-white mb-4">
                            <i class="fas fa-address-card me-2 text-white"></i>Contact Info
                        </h4>
                        
                        <div class="mb-4">
                            <div class="d-flex align-items-start mb-4 contact-info-item">
                                <div class="flex-shrink-0">
                                    <div class="d-flex align-items-center justify-content-center rounded-circle" style="width: 48px; height: 48px; background: rgba(255,255,255,0.1);">
                                        <i class="fas fa-map-marker-alt text-accent"></i>
                                    </div>
                                </div>
                                <div class="ms-3">
                                    <h6 class="fw-bold text-white mb-1">Address</h6>
                                    <p class="mb-0 text-white opacity-75">Rajkot, Gujarat</p>
                                </div>
                            </div>
                            
                            <div class="d-flex align-items-start mb-4 contact-info-item">
                                <div class="flex-shrink-0">
                                    <div class="d-flex align-items-center justify-content-center rounded-circle" style="width: 48px; height: 48px; background: rgba(255,255,255,0.1);">
                                        <i class="fas fa-phone text-accent"></i>
                                    </div>
                                </div>
                                <div class="ms-3">
                                    <h6 class="fw-bold text-white mb-1">Phone</h6>
                                    <p class="mb-0 text-white opacity-75"><a href="tel:+919876543210" class="text-white text-decoration-none">+91 9876543210</a></p>
                                </div>
                            </div>
                            
                            <div class="d-flex align-items-start mb-4 contact-info-item">
                                <div class="flex-shrink-0">
                                    <div class="d-flex align-items-center justify-content-center rounded-circle" style="width: 48px; height: 48px; background: rgba(255,255,255,0.1);">
                                        <i class="fas fa-envelope text-accent"></i>
                                    </div>
                                </div>
                                <div class="ms-3">
                                    <h6 class="fw-bold text-white mb-1">Email</h6>
                                    <p class="mb-0 text-white opacity-75"><a href="mailto:cloud9cafe.website@gmail.com" class="text-white text-decoration-none">cloud9cafe.website@gmail.com</a></p>
                                </div>
                            </div>
                            
                            <div class="d-flex align-items-start contact-info-item">
                                <div class="flex-shrink-0">
                                    <div class="d-flex align-items-center justify-content-center rounded-circle" style="width: 48px; height: 48px; background: rgba(255,255,255,0.1);">
                                        <i class="fas fa-clock text-accent"></i>
                                    </div>
                                </div>
                                <div class="ms-3">
                                    <h6 class="fw-bold text-white mb-1">Hours</h6>
                                    <p class="mb-0 text-white opacity-75">Mon - Fri: 7AM - 10PM<br>Sat - Sun: 8AM - 11PM</p>
                                </div>
                            </div>
                        </div>
                        
                        <hr style="border-color: rgba(255,255,255,0.1);">
                        
                        <h6 class="fw-bold text-white mb-3">Follow Us</h6>
                        <div class="d-flex gap-2">
                            <a href="#" class="social-icon d-flex align-items-center justify-content-center rounded-circle text-white" style="width: 40px; height: 40px; background: rgba(255,255,255,0.1); transition: all 0.3s;">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="#" class="social-icon d-flex align-items-center justify-content-center rounded-circle text-white" style="width: 40px; height: 40px; background: rgba(255,255,255,0.1); transition: all 0.3s;">
                                <i class="fab fa-instagram"></i>
                            </a>
                            <a href="#" class="social-icon d-flex align-items-center justify-content-center rounded-circle text-white" style="width: 40px; height: 40px; background: rgba(255,255,255,0.1); transition: all 0.3s;">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="#" class="social-icon d-flex align-items-center justify-content-center rounded-circle text-white" style="width: 40px; height: 40px; background: rgba(255,255,255,0.1); transition: all 0.3s;">
                                <i class="fab fa-youtube"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="py-5" style="background: var(--bg-cream);">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center mb-5 animate-on-scroll">
                <span class="badge bg-primary bg-opacity-10 text-white mb-3">FAQ</span>
                <h2 class="fw-bold">Frequently Asked Questions</h2>
                <p class="text-muted">Quick answers to common questions</p>
            </div>
        </div>
        
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="accordion" id="contactFaq">
                    <div class="accordion-item border-0 shadow-sm mb-3 animate-on-scroll stagger-1">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                <i class="fas fa-question-circle text-primary me-2"></i>Do you offer catering services?
                            </button>
                        </h2>
                        <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#contactFaq">
                            <div class="accordion-body">
                                Yes! We offer catering for events of all sizes. Please contact us at least 48 hours in advance for small orders and one week for larger events.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item border-0 shadow-sm mb-3 animate-on-scroll stagger-2">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                <i class="fas fa-question-circle text-primary me-2"></i>Can I make a reservation?
                            </button>
                        </h2>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#contactFaq">
                            <div class="accordion-body">
                                We accept reservations for groups of 6 or more. For smaller groups, we operate on a walk-in basis. Call us or use the contact form to book your table.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item border-0 shadow-sm animate-on-scroll stagger-3">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                <i class="fas fa-question-circle text-primary me-2"></i>Do you have WiFi available?
                            </button>
                        </h2>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#contactFaq">
                            <div class="accordion-body">
                                Yes! We offer complimentary high-speed WiFi for all our customers. Ask our staff for the password when you order.
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- View More FAQ Button -->
                <div class="text-center mt-4 animate-on-scroll">
                    <a href="faq.php" class="btn btn-outline-primary btn-lg">
                        <i class="fas fa-question-circle me-2"></i>View More FAQ
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
/* Validation styles */
.is-valid {
    border-color: #198754 !important;
}

.is-invalid {
    border-color: #dc3545 !important;
}

/* Contact info hover effect */
.contact-info-item {
    transition: transform 0.3s ease;
}

.contact-info-item:hover {
    transform: translateX(5px);
}

/* Social icon hover effect */
.social-icon:hover {
    background: var(--cafe-accent) !important;
    color: var(--cafe-primary-dark) !important;
    transform: translateY(-3px);
}

/* Accordion styling */
.accordion-button {
    font-weight: 500;
    padding: 1.25rem;
}

.accordion-button:not(.collapsed) {
    background: rgba(107, 79, 75, 0.05);
    color: var(--cafe-primary);
}

.accordion-button:focus {
    box-shadow: none;
    border-color: rgba(107, 79, 75, 0.25);
}

.accordion-body {
    color: var(--text-medium);
    padding: 1.25rem;
}

/* Button loading state */
.btn-loading {
    position: relative;
    color: transparent !important;
}

.btn-loading::after {
    content: '';
    position: absolute;
    width: 20px;
    height: 20px;
    top: 50%;
    left: 50%;
    margin-left: -10px;
    margin-top: -10px;
    border: 2px solid #ffffff;
    border-radius: 50%;
    border-top-color: transparent;
    animation: spinner 0.8s linear infinite;
}

@keyframes spinner {
    to { transform: rotate(360deg); }
}
</style>

<script src="../assets/js/jquery.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('contactForm');
    const messageField = document.getElementById('message');
    const charCount = document.getElementById('charCount');
    const submitBtn = document.getElementById('submitBtn');
    
    // Character counter for message
    messageField.addEventListener('input', function() {
        charCount.textContent = this.value.length;
    });
    
    // Initialize character count
    charCount.textContent = messageField.value.length;
    
    // Form validation
    form.addEventListener('submit', function(e) {
        let isValid = true;
        
        // Validate required fields
        const requiredFields = form.querySelectorAll('[required]');
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                isValid = false;
            } else {
                field.classList.remove('is-invalid');
                field.classList.add('is-valid');
            }
        });
        
        // Validate email
        const emailField = document.getElementById('email');
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailPattern.test(emailField.value)) {
            emailField.classList.add('is-invalid');
            isValid = false;
        }
        
        // Validate message length
        if (messageField.value.length < 10) {
            messageField.classList.add('is-invalid');
            isValid = false;
        }
        
        if (!isValid) {
            e.preventDefault();
        } else {
            // Show loading state
            submitBtn.classList.add('btn-loading');
            submitBtn.disabled = true;
        }
    });
    
    // Real-time validation feedback
    const inputs = form.querySelectorAll('input, textarea');
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            if (this.value.trim()) {
                if (this.type === 'email') {
                    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (emailPattern.test(this.value)) {
                        this.classList.remove('is-invalid');
                        this.classList.add('is-valid');
                    } else {
                        this.classList.add('is-invalid');
                        this.classList.remove('is-valid');
                    }
                } else if (this.hasAttribute('minlength')) {
                    const minLength = parseInt(this.getAttribute('minlength'));
                    if (this.value.length >= minLength) {
                        this.classList.remove('is-invalid');
                        this.classList.add('is-valid');
                    } else {
                        this.classList.add('is-invalid');
                        this.classList.remove('is-valid');
                    }
                } else {
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                }
            }
        });
        
        input.addEventListener('input', function() {
            if (this.classList.contains('is-invalid') && this.value.trim()) {
                this.classList.remove('is-invalid');
            }
        });
    });
});
</script>

<?php
$content = ob_get_clean();
include '../includes/layout.php';
?>
