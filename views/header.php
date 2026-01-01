<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="/assets/styles/app.css">
    <link rel="stylesheet" href="/assets/styles/calendar.css">
    <link rel="stylesheet" href="/assets/styles/booking.css">
    <link rel="stylesheet" href="/assets/styles/food.css">
    <link rel="icon" href="/assets/images/favicon_64.png">
    <title>Yrgopelag</title>
</head>

<?php 
// Start session if not already started to check login status
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$stmtStars = $database->query("SELECT hotel_stars FROM settings WHERE id = 1");
$starCount = (int)$stmtStars->fetchColumn();
?>

<body>
    <header>
        <nav>
            <a class="home-link" href="/index.php">
                <img class="logo" src="/assets/images/logo.png" alt="logo">
                <div class="hotel-info">
                    <h1 class="hotelname">The Semicolon Sanctuary</h1>
                    <div class="hotel-stars">
                        <?php for ($i = 0; $i < $starCount; $i++) {
                            echo "⭐"; 
                        } ?>
                    </div>
                </div>
            </a>
            <i> <?php $starId = 1 ?></i>
            <ul>
                <li><a href="/index.php">Home</a></li>
                <li><a href="/app/src/booking.php">Booking</a></li>
                <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
                    <li><a href="/app/src/admin.php">Admin Dashboard</a></li>
                    <li><a href="/app/src/logout.php" style="color: rgb(255, 88, 88); font-weight: 700;">Logout</a></li>
                <?php else: ?>
                    <li><a href="/app/src/login.php">Owner Login</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
   