<?php 
require (__DIR__ . '/vendor/autoload.php');
require (__DIR__ . '/app/src/autoload.php');
global $database;
// require_once (__DIR__ . '/app/src/functions.php');
require (__DIR__ . '/views/header.php'); 







?>

    <section class="rooms">
        <div class="room">
            <h3>Budget</h3>
            <p>The Cave</p>
            <img src="https://www.ilmondo.com.au/wp-content/uploads/2023/08/il-mondo-kangaroo-point-accommodation-budget-room-bed.jpg" alt="budget-room">
            <?php $roomId = 1; // Budget Room
            include 'views/calendar.php'; ?>
            <button>add activities +</button>
        </div>
        <div class="room">
            <h3>Standard</h3>
            <p>The Bungalow</p>
            <img src="https://r.profitroom.com/premierhotelthewinkler/images/rooms/0911e3f7-cdfd-4964-8888-2cc66d03f35e.jpg" alt="standard-room">
            <?php $roomId = 2; // Standard Room
            include 'views/calendar.php'; ?>
        </div>
        <div class="room">
            <h3>Luxery</h3>
            <p>The Volcano Suite</p>
            <img src="https://media.architecturaldigest.com/photos/659d9cb42446c7171718ecf0/master/w_1600%2Cc_limit/atr.royalmansion-bedroom2-mr.jpg" alt="luxery-room">
            <?php $roomId = 3; // Luxury Room
            include 'views/calendar.php'; ?>
        </div>
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


