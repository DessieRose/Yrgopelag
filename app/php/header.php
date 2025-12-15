<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="/style.css">
    <title>Yrgopelag</title>
</head>
<body>
    <header>
        <nav class="nav-left">
            <ul>
                <li><img src="" alt="logo"></li>
            </ul>
        </nav>
        <nav>
            <ul class="nav-right">
                <li><a href="">Packadges</a></li>
                <li><a href="">Features</a></li>
                <li><a href="">Login</a></li>
            </ul>
        </nav>
    </header>
    <div class="hero-img">
        <img src="" alt="hero-img">
        <div class="stars" id="stars">
            <?php $stars = (int)$settings['stars'];
            echo str_repeat('★', $stars); ?>
        </div>
    </div>