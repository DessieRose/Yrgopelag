<?php
declare(strict_types=1);

require __DIR__ . '/autoload.php';

try {
    $db = new PDO("sqlite:$dbPath");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1. Clear the junction table first (links bookings to features)
    $db->exec("DELETE FROM booking_features");
    
    // 2. Clear the main bookings table
    $db->exec("DELETE FROM bookings");

    // 3. Optional: Reset the ID counter so the next booking starts at ID 1
    // $db->exec("DELETE FROM sqlite_sequence WHERE name='bookings'");
    // $db->exec("DELETE FROM sqlite_sequence WHERE name='booking_features'");

    echo "✅ All bookings and associated features have been cleared successfully!";
} catch (Exception $e) {
    echo "❌ Error clearing database: " . $e->getMessage();
}