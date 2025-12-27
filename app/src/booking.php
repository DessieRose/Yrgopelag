<?php 
require (__DIR__ . '/autoload.php');
require (__DIR__ . '/../../views/header.php'); 

$stmtRooms = $database->query("SELECT * FROM rooms");
$rooms = $stmtRooms->fetchAll(PDO::FETCH_ASSOC);

// $stmtFeatures = $database->query("SELECT * FROM features WHERE active = 1");
// $features = $stmtFeatures->fetchAll(PDO::FETCH_ASSOC);

// Fetch active features and their activity names
$query = "
    SELECT 
    features.*, 
    activities.name AS activity_name 
FROM features 
JOIN activities ON features.activity_id = activities.id 
WHERE features.active = 1
ORDER BY activities.id ASC, features.id ASC";

$stmt = $database->query($query);
$allFeatures = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Group features by activity name
$groupedFeatures = [];
foreach ($allFeatures as $feature) {
    $groupedFeatures[$feature['activity_name']][] = $feature;
}


$selectedRoomId = $_GET['room_id'] ?? 1;
?>

<main class="booking-container">
    <h2>Book a room</h2>
    

    <form action="/app/src/process_booking.php" method="POST" class="booking-form">
        <div class="form-group">
                <label for="user_name">Name (UserID):</label>
                <input type="text" id="user_name" name="user_name" required placeholder="John">
            </div>

            <div class="form-group">
                <label for="transfer_code">Transfer Code:</label>
                <input type="text" id="transfer_code" name="transfer_code" required placeholder="XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX">
            </div>

            <div class="form-group">
                <label for="room_id">Select Room:</label>
                <select name="room_id" id="room_id" required>
                    <?php foreach ($rooms as $room): ?>
                        <option value="<?= $room['id']; ?>" data-price="<?= $room['price']; ?>" <?= $room['id'] == $selectedRoomId ? 'selected' : ''; ?>>
                            <?= $room['type']; ?> ($<?= $room['price']; ?>/night)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="date-inputs">
                <div class="form-group">
                    <label for="arrival-<?= $selectedRoomId; ?>">Arrival Date (15:00)</label>
                    <input type="date" id="arrival-<?= $roomId; ?>"name="arrival_date" required min="2026-01-01" max="2026-01-30">
                </div>

                <div class="form-group">
                    <label for="departure-<?= $roomId; ?>">Departure Date (11:00)</label>
                    <input type="date" id="departure-<?= $roomId; ?>" name="departure_date" required min="2026-01-02" max="2026-01-31"> </div>
                </div>
            </div>

            <div class="features-selection">
                <h3>Extra Features</h3>

                <?php foreach ($groupedFeatures as $activityName => $features): ?>
                    <div class="activity-group">
                        <h4><?= ucfirst(htmlspecialchars($activityName)); ?></h4>
                        
                        <?php foreach ($features as $feature): ?>
                            <div class="feature-item">
                                <input type="checkbox" 
                                    name="features[]" 
                                    class="feature-checkbox" 
                                    id="feat-<?= $feature['id']; ?>" 
                                    value="<?= $feature['id']; ?>" 
                                    data-price="<?= $feature['price']; ?>">
                                <label for="feat-<?= $feature['id']; ?>">
                                    <?= htmlspecialchars($feature['name']); ?> 
                                    (+$<?= $feature['price']; ?>)
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="summary-box">
            <div id="booking-summary">
                <p>Total Cost: <span id="display-total">$0</span></p>
            </div>
            <button type="submit" class="complete-button">Complete Booking</button>
        </div>
    </form>
</main>
           
<?php require (__DIR__ . '/../../views/footer.php'); ?>