<?php
declare(strict_types=1);
require (__DIR__ . '/autoload.php');

session_start();

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Check against .env variables
    if ($username === $_ENV['ISLAND_USER'] && $password === $_ENV['API_KEY']) {
        $_SESSION['logged_in'] = true;
        header('Location: admin.php');
        exit;
    } else {
        $error = "Invalid username or API key.";
    }
}
?>

<main>
    <form method="POST" style="max-width:300px; margin: 50px auto;">
        <h1>Admin Login</h1>
        <?php if ($error): ?> <p style="color:red;"><?= $error; ?></p> <?php endif; ?>
        <input type="text" name="username" placeholder="Username" required style="display:block; width:100%; margin-bottom:10px;">
        <input type="password" name="password" placeholder="API Key" required style="display:block; width:100%; margin-bottom:10px;">
        <button type="submit">Login</button>
    </form>
</main>