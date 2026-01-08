<?php
declare(strict_types=1);
$database = new PDO('sqlite:' . __DIR__ . '/../database/hotel.db');

echo "<h1>Database Write Test</h1>";

try {
    // 1. Verify the path being used
    echo "Attempting to write to: " . realpath(__DIR__ . '/../database/hotel.db') . "<br>";

    // 2. Prepare a dummy insert
    // Adjust column names if they differ in your table (e.g., 'user_name' vs 'guest_name')
    $stmt = $database->prepare("
        INSERT INTO bookings (
            room_id, 
            user_name, 
            arrival_date, 
            departure_date, 
            total_cost, 
            transfer_code, 
            discount_amount
        ) VALUES (1, 'TestUser', '2026-01-20', '2026-01-21', 100, 'test-code-123', 0)
    ");

    $stmt->execute();

    echo "✅ Success! One row inserted.<br>";
    
    // 3. Immediately read it back to confirm
    $check = $database->query("SELECT * FROM bookings WHERE user_name = 'TestUser' LIMIT 1")->fetch();
    
    if ($check) {
        echo "🎉 Confirmation: Found 'TestUser' in the database!";
    }

} catch (PDOException $e) {
    echo "❌ Database Error: " . $e->getMessage();
} catch (Exception $e) {
    echo "❌ General Error: " . $e->getMessage();
}