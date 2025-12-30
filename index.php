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
    <img src="/assets/images/hotel-images/hero-image_2.png" alt="hero-img">
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
        <img src="https://cdn.prod.website-files.com/62a1ad9e66ad7514469f0685/65ca12eaa3f9de8456ba4be5_blog-hero%20image-dining%20experience.jpg" alt="dinner-img">
    </section>

    <section class="activities">
        <h2 class="text-float">Activities</h2>
        <img src="https://www.anglershotelmiami.com/images/1700-960/rpp_kimpton_ang_pool_deck_final-1-2-1bce4bc5.jpg" alt="activities">
    </section>
    






<?php require (__DIR__ . '/views/footer.php'); ?>


