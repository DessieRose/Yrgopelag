<?php 
// require (__DIR__ . '/vendor/autoload.php');
// require (__DIR__ . '/autoload.php');
require (__DIR__ . '/../../views/header.php'); 
global $database;

?>
    <section class="rooms">
        <div class="room">
            <h3>Budget</h3>
            <p>The Cave</p>
            <?php $roomId = 1; // Budget Room
            include '/../../views/calendar.php'; ?>
        </div>
        <div class="room">
            <h3>Standard</h3>
            <p>The Bungalow</p>
            <?php $roomId = 2; // Standard Room
            include 'views/calendar.php'; ?>
        </div>
        <div class="room">
            <h3>Luxury</h3>
            <p>The Volcano Suite</p>
            <?php $roomId = 3; // Luxury Room
            include 'views/calendar.php'; ?>
        </div>
    </section>
    
<?php require (__DIR__ . '/../../views/footer.php'); ?>


