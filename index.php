<?php 
require (__DIR__ . '/vendor/autoload.php');
require (__DIR__ . '/app/src/autoload.php');
require (__DIR__ . '/views/header.php'); 
global $database;

// Fetch the discount from settings
$stmtSettings = $database->query("SELECT discount_percent FROM settings WHERE id = 1");
$discountPercent = $stmtSettings->fetchColumn();
?>

<div class="hero-img">
    <h2 class="text-float title">The Semicolon Sanctuary</h2>
    <img src="/assets/images/hotel-images/hero-image_3_hero.png" alt="hero-img">
</div>
    <section class="rooms">
        <div class="room">
            <h3>Budget</h3>
            <p>The Cave</p>
            <img src="/assets/images/hotel-images/rooms/room_budget.png" alt="budget-room">
            <?php $roomId = 1; // Budget Room
            include 'views/calendar.php'; ?>
            <a class="book-button" href="/app/src/booking.php?room_id=<?= $roomId; ?>" class="btn">Book Now</a>
        </div>
        <div class="room">
            <h3>Standard</h3>
            <p>The Bungalow</p>
            <img src="/assets/images/hotel-images/rooms/room_standard.png" alt="standard-room">
            <?php $roomId = 2; // Standard Room
            include 'views/calendar.php'; ?>
            <a class="book-button" href="/app/src/booking.php?room_id=<?= $roomId; ?>" class="btn">Book Now</a>
        </div>
        <div class="room">
            <h3>Luxury</h3>
            <p>The Volcano Suite</p>
            <img src="/assets/images/hotel-images/rooms/room_luxury.png" alt="luxery-room">
            <?php $roomId = 3; // Luxury Room
            include 'views/calendar.php'; ?>
            <a class="book-button" href="/app/src/booking.php?room_id=<?= $roomId; ?>" class="btn">Book Now</a>
        </div>
    </section>

    <section class="loyalty-promo">
        <h2>Welcome Back! 🌴</h2>
        <p class="peragraph">Returning guests automatically receive a <strong><?= $discountPercent; ?>% loyalty discount</strong> on their entire booking!</p>
        <small>Simply use the same name you used for your previous stay.</small>
    </section>
    
    <section class="dinner-plans">
        <h2 class="text-float">Dinner Plans</h2>
        <!-- <img src="https://cdn.prod.website-files.com/62a1ad9e66ad7514469f0685/65ca12eaa3f9de8456ba4be5_blog-hero%20image-dining%20experience.jpg" alt="dinner-img"> -->
        <img src="/assets/images/hotel-images/restaurant.png" alt="">
    </section>

    <section class="restaurants">
        <div class="restaurant-intro">
            <h2>Culinary Experiences</h2>
            <p class="peragraph">Indulge in our three world-class dining venues.</p>
        </div>

        <div class="restaurant-grid">
            <div class="restaurant-card">
                <div class="card-icon">🐟</div>
                <h3>Azure Depths</h3>
                <p class="cuisine-type">Fine Seafood & Champagne</p>
                <p>Dive into a world of flavor. Our signature restaurant offers the freshest catch from the surrounding archipelago, served in an elegant glass-walled dining room overlooking the ocean.</p>
            </div>

            <div class="restaurant-card">
                <div class="card-icon">🔥</div>
                <h3>The Magma Grill</h3>
                <p class="cuisine-type">Charcoal Steakhouse</p>
                <p>Experience the heat. We cook our premium cuts on superheated volcanic stones right at your table. Rich flavors, smoky aromas, and the island's best wine cellar.</p>
            </div>

            <div class="restaurant-card">
                <div class="card-icon">🥥</div>
                <h3>Coco's Cabana</h3>
                <p class="cuisine-type">Tropical Fusion & Bar</p>
                <p>Kick off your shoes. Enjoy wood-fired pizzas, zesty fish tacos, and our famous coconut rum punch while watching the sun dip below the horizon.</p>
            </div>
        </div>
    </section>

    <section class="features-intro">
        <a class="link-fetures-img" href="/app/src/booking.php">
            <h2 class="text-float">Island Features</h2>
            <!-- <img src="https://www.anglershotelmiami.com/images/1700-960/rpp_kimpton_ang_pool_deck_final-1-2-1bce4bc5.jpg" alt="activities"> -->
            <img src="/assets/images/hotel-images/features.png" alt="">
        </a>
    </section>
    
    
<?php require (__DIR__ . '/views/footer.php'); ?>


