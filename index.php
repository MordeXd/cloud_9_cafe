<?php
$pageTitle = 'Home - Cloud 9 Cafe';
include 'includes/header.php';
?>
<div class="container">
    <section class="hero-section text-center mb-5">
        <h1 class="display-5 fw-bold">Welcome to Cloud 9 Cafe</h1>
        <p class="lead">Fresh coffee, warm meals, and a calm space to relax.</p>
        <a href="menu.php" class="btn btn-cafe btn-lg mt-2">Explore Menu</a>
    </section>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card section-card h-100">
                <div class="card-body">
                    <h3 class="h5">Freshly Brewed Coffee</h3>
                    <p class="mb-0">Taste handcrafted coffee made with care every day.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card section-card h-100">
                <div class="card-body">
                    <h3 class="h5">Comfort Food</h3>
                    <p class="mb-0">Enjoy snacks, desserts, and signature cafe dishes.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card section-card h-100">
                <div class="card-body">
                    <h3 class="h5">Easy Online Booking</h3>
                    <p class="mb-0">Reserve a table online and manage your bookings simply.</p>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>