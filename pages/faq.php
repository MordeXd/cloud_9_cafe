<?php
$title = "FAQ - Cloud 9 Cafe";
ob_start();
?>

<!-- Page Header -->
<section class="py-5" style="background: var(--gradient-primary);">
    <div class="container text-center text-white py-4">
        <h1 class="fw-bold text-white mb-2">Frequently Asked Questions</h1>
        <p class="mb-0 text-white opacity-75">Everything you need to know about Cloud 9 Cafe</p>
    </div>
</section>

<div class="container py-5 fade-in-up">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            
            <!-- Search FAQ -->
            <div class="card border-0 shadow-sm mb-5">
                <div class="card-body p-4 text-center">
                    <h5 class="fw-bold mb-3" style="color: var(--cafe-primary);">How can we help you?</h5>
                    <div class="position-relative max-w-500 mx-auto">
                        <i class="fas fa-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                        <input type="text" class="form-control ps-5" id="faqSearch" placeholder="Search questions..." style="border-radius: 50px;">
                    </div>
                </div>
            </div>

            <div class="accordion" id="faqAccordion">

                <!-- Section: Orders & Delivery -->
                <div class="mb-4">
                    <h4 class="fw-bold mb-3" style="color: var(--cafe-primary);">
                        <i class="fas fa-motorcycle me-2 text-accent"></i>Orders & Delivery
                    </h4>

                    <div class="accordion-item border-0 mb-3 rounded shadow-sm overflow-hidden">
                        <h2 class="accordion-header" id="headingOrder1">
                            <button class="accordion-button fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOrder1" aria-expanded="true" aria-controls="collapseOrder1" style="color: var(--cafe-primary);">
                                How do I place an order?
                            </button>
                        </h2>
                        <div id="collapseOrder1" class="accordion-collapse collapse show" aria-labelledby="headingOrder1" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-secondary">
                                Ordering is easy! Browse our menu, select your favorite coffee or snacks, add them to your cart, and proceed to checkout. You can place an order as a guest or create an account for faster checkout and order tracking. We accept Cash on Delivery and online payments.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item border-0 mb-3 rounded shadow-sm overflow-hidden">
                        <h2 class="accordion-header" id="headingOrder2">
                            <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOrder2" aria-expanded="false" aria-controls="collapseOrder2" style="color: var(--cafe-primary);">
                                What are your delivery hours?
                            </button>
                        </h2>
                        <div id="collapseOrder2" class="accordion-collapse collapse" aria-labelledby="headingOrder2" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-secondary">
                                We deliver from <strong>7:00 AM to 10:00 PM</strong> every day, including weekends and holidays. Last order for delivery is accepted at 9:30 PM to ensure your coffee arrives fresh and hot!
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item border-0 mb-3 rounded shadow-sm overflow-hidden">
                        <h2 class="accordion-header" id="headingOrder3">
                            <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOrder3" aria-expanded="false" aria-controls="collapseOrder3" style="color: var(--cafe-primary);">
                                How long does delivery take?
                            </button>
                        </h2>
                        <div id="collapseOrder3" class="accordion-collapse collapse" aria-labelledby="headingOrder3" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-secondary">
                                We guarantee delivery within <strong>30 minutes</strong> for all orders within 5km of our cafe. For locations beyond that, delivery may take 45-60 minutes. You can track your order status in real-time through your account dashboard.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item border-0 mb-3 rounded shadow-sm overflow-hidden">
                        <h2 class="accordion-header" id="headingOrder4">
                            <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOrder4" aria-expanded="false" aria-controls="collapseOrder4" style="color: var(--cafe-primary);">
                                Is there a minimum order amount?
                            </button>
                        </h2>
                        <div id="collapseOrder4" class="accordion-collapse collapse" aria-labelledby="headingOrder4" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-secondary">
                                Yes, we have a minimum order amount of <strong>₹200</strong> for delivery orders. There is no minimum for pickup orders. Delivery charges are ₹30 for orders below ₹500 and FREE for orders above ₹500.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section: Menu & Customization -->
                <div class="mb-4">
                    <h4 class="fw-bold mb-3 mt-5" style="color: var(--cafe-primary);">
                        <i class="fas fa-coffee me-2 text-accent"></i>Menu & Customization
                    </h4>

                    <div class="accordion-item border-0 mb-3 rounded shadow-sm overflow-hidden">
                        <h2 class="accordion-header" id="headingMenu1">
                            <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseMenu1" aria-expanded="false" aria-controls="collapseMenu1" style="color: var(--cafe-primary);">
                                Can I customize my coffee order?
                            </button>
                        </h2>
                        <div id="collapseMenu1" class="accordion-collapse collapse" aria-labelledby="headingMenu1" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-secondary">
                                Absolutely! You can customize your coffee with options like extra shot, different milk choices (whole, skim, almond, oat), sugar levels, and temperature preferences. Add your preferences in the "Special Instructions" box when adding items to your cart.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item border-0 mb-3 rounded shadow-sm overflow-hidden">
                        <h2 class="accordion-header" id="headingMenu2">
                            <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseMenu2" aria-expanded="false" aria-controls="collapseMenu2" style="color: var(--cafe-primary);">
                                Do you offer vegan or dairy-free options?
                            </button>
                        </h2>
                        <div id="collapseMenu2" class="accordion-collapse collapse" aria-labelledby="headingMenu2" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-secondary">
                                Yes! We offer oat milk, almond milk, and soy milk as dairy alternatives at no extra charge. We also have vegan pastries and snacks. Look for the 🌱 (vegan) indicator on our menu items.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item border-0 mb-3 rounded shadow-sm overflow-hidden">
                        <h2 class="accordion-header" id="headingMenu3">
                            <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseMenu3" aria-expanded="false" aria-controls="collapseMenu3" style="color: var(--cafe-primary);">
                                Are your ingredients fresh and locally sourced?
                            </button>
                        </h2>
                        <div id="collapseMenu3" class="accordion-collapse collapse" aria-labelledby="headingMenu3" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-secondary">
                                We pride ourselves on using freshly roasted coffee beans sourced from ethical farms in Colombia, Ethiopia, and Brazil. Our pastries are baked fresh daily in our kitchen, and we source dairy and produce from local farms whenever possible.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section: Rewards & Loyalty -->
                <div class="mb-4">
                    <h4 class="fw-bold mb-3 mt-5" style="color: var(--cafe-primary);">
                        <i class="fas fa-gift me-2 text-accent"></i>Rewards & Loyalty
                    </h4>

                    <div class="accordion-item border-0 mb-3 rounded shadow-sm overflow-hidden">
                        <h2 class="accordion-header" id="headingReward1">
                            <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseReward1" aria-expanded="false" aria-controls="collapseReward1" style="color: var(--cafe-primary);">
                                How does the loyalty program work?
                            </button>
                        </h2>
                        <div id="collapseReward1" class="accordion-collapse collapse" aria-labelledby="headingReward1" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-secondary">
                                Earn 10 reward points for every order you place! Collect 100 points and get ₹50 off your next order. Points are automatically added to your account after each successful delivery. You can track your points in your dashboard.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item border-0 mb-3 rounded shadow-sm overflow-hidden">
                        <h2 class="accordion-header" id="headingReward2">
                            <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseReward2" aria-expanded="false" aria-controls="collapseReward2" style="color: var(--cafe-primary);">
                                Do you offer birthday rewards?
                            </button>
                        </h2>
                        <div id="collapseReward2" class="accordion-collapse collapse" aria-labelledby="headingReward2" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-secondary">
                                Yes! Members receive a special <strong>FREE coffee</strong> on their birthday! Make sure to add your birthdate to your profile to receive this perk. The reward is valid for 7 days from your birthday.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section: Account & Support -->
                <div class="mb-4">
                    <h4 class="fw-bold mb-3 mt-5" style="color: var(--cafe-primary);">
                        <i class="fas fa-user-circle me-2 text-accent"></i>Account & Support
                    </h4>

                    <div class="accordion-item border-0 mb-3 rounded shadow-sm overflow-hidden">
                        <h2 class="accordion-header" id="headingAccount1">
                            <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAccount1" aria-expanded="false" aria-controls="collapseAccount1" style="color: var(--cafe-primary);">
                                How do I create an account?
                            </button>
                        </h2>
                        <div id="collapseAccount1" class="accordion-collapse collapse" aria-labelledby="headingAccount1" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-secondary">
                                Click on "Register" in the top menu and fill in your details. You'll need to provide your name, email, phone number, and delivery address. Creating an account allows you to track orders, earn rewards, and enjoy faster checkout.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item border-0 mb-3 rounded shadow-sm overflow-hidden">
                        <h2 class="accordion-header" id="headingAccount2">
                            <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAccount2" aria-expanded="false" aria-controls="collapseAccount2" style="color: var(--cafe-primary);">
                                I forgot my password. What should I do?
                            </button>
                        </h2>
                        <div id="collapseAccount2" class="accordion-collapse collapse" aria-labelledby="headingAccount2" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-secondary">
                                Click on "Forgot Password" on the login page, enter your registered email address, and we'll send you a link to reset your password. For security, the link expires in 1 hour.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item border-0 mb-3 rounded shadow-sm overflow-hidden">
                        <h2 class="accordion-header" id="headingAccount3">
                            <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAccount3" aria-expanded="false" aria-controls="collapseAccount3" style="color: var(--cafe-primary);">
                                How can I contact customer support?
                            </button>
                        </h2>
                        <div id="collapseAccount3" class="accordion-collapse collapse" aria-labelledby="headingAccount3" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-secondary">
                                You can reach us through:<br>
                                • <strong>Phone:</strong> +91 98765 43210 (7 AM - 10 PM)<br>
                                • <strong>Email:</strong> support@cloud9cafe.com<br>
                                • <strong>Contact Form:</strong> Visit our <a href="contact.php" style="color: var(--cafe-primary);">Contact Us</a> page<br>
                                We typically respond within 2 hours during business hours.
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Contact CTA -->
            <div class="card border-0 shadow-sm mt-5 text-center" style="background: linear-gradient(135deg, var(--bg-cream) 0%, #fff 100%);">
                <div class="card-body p-5">
                    <i class="fas fa-comments fa-3x mb-3" style="color: var(--cafe-primary);"></i>
                    <h4 class="fw-bold mb-2" style="color: var(--cafe-primary);">Still have questions?</h4>
                    <p class="text-muted mb-4">Can't find the answer you're looking for? Our friendly team is here to help!</p>
                    <a href="contact.php" class="btn btn-primary px-4">
                        <i class="fas fa-envelope me-2"></i>Contact Us
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>

<style>
    .max-w-500 {
        max-width: 500px;
    }
    
    /* Custom accordion styling */
    .accordion-button:not(.collapsed) {
        background-color: rgba(107, 79, 75, 0.05);
        box-shadow: none;
    }
    
    .accordion-button:focus {
        box-shadow: none;
        border-color: rgba(107, 79, 75, 0.2);
    }
    
    .accordion-button::after {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%236B4F4B'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");
    }
    
    .text-accent {
        color: var(--cafe-accent) !important;
    }
</style>

<script>
    // FAQ Search functionality
    document.getElementById('faqSearch').addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const accordionItems = document.querySelectorAll('.accordion-item');
        
        accordionItems.forEach(item => {
            const question = item.querySelector('.accordion-button').textContent.toLowerCase();
            const answer = item.querySelector('.accordion-body').textContent.toLowerCase();
            
            if (question.includes(searchTerm) || answer.includes(searchTerm)) {
                item.style.display = 'block';
            } else {
                item.style.display = searchTerm === '' ? 'block' : 'none';
            }
        });
    });
</script>

<?php
$content = ob_get_clean();
include '../includes/layout.php';
?>
