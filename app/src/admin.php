<?php
declare(strict_types=1);
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require (__DIR__ . '/autoload.php');
require (__DIR__ . '/../../views/header.php'); 

// Handle Form Submission (POST)
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $database->beginTransaction();

        // Update General Settings (Stars & Discount)
        $stars = (int)$_POST['hotel_stars'];
        $discount = (int)$_POST['discount_percent'];
        
        $stmtSettings = $database->prepare("UPDATE settings SET hotel_stars = ?, discount_percent = ? WHERE id = 1");
        $stmtSettings->execute([$stars, $discount]);

        // B. Update Room Prices
        if (isset($_POST['room_prices'])) {
            foreach ($_POST['room_prices'] as $roomId => $price) {
                $stmtRoom = $database->prepare("UPDATE rooms SET price = ? WHERE id = ?");
                $stmtRoom->execute([(float)$price, (int)$roomId]);
            }
        }

        // C. Update Features (Active Status)
        // First, set all to inactive (0), then strictly enable the checked ones
        $database->exec("UPDATE features SET active = 0");

        if (isset($_POST['active_features'])) {
            foreach ($_POST['active_features'] as $featureId) {
                $stmtFeat = $database->prepare("UPDATE features SET active = 1 WHERE id = ?");
                $stmtFeat->execute([(int)$featureId]);
            }
        }

        $database->commit();
        $message = "Settings saved successfully!";

    } catch (Exception $e) {
        if ($database->inTransaction()) {
            $database->rollBack();
        }
        $message = "Error saving settings: " . $e->getMessage();
    }
}

// 2. Fetch Data to Display
// Get Settings
$stmtSettings = $database->query("SELECT * FROM settings WHERE id = 1");
$settings = $stmtSettings->fetch(PDO::FETCH_ASSOC);

// Get Rooms
$stmtRooms = $database->query("SELECT * FROM rooms");
$rooms = $stmtRooms->fetchAll(PDO::FETCH_ASSOC);

// Get Features
$stmtFeatures = $database->query("SELECT * FROM features");
$features = $stmtFeatures->fetchAll(PDO::FETCH_ASSOC);

?>

<main class="admin-container">
    <h1>Admin Dashboard</h1>

    <?php if ($message): ?>
        <div class="alert"><?= $message; ?></div>
    <?php endif; ?>

    <form action="admin.php" method="POST" class="admin-form">
        
        <section class="admin-section">
            <h2>General Hotel Settings</h2>
            <div class="form-group">
                <label for="stars">Hotel Stars (1-5):</label>
                <input type="number" id="stars" name="hotel_stars" 
                       value="<?= $settings['hotel_stars']; ?>" min="1" max="5">
            </div>
            
            <div class="form-group">
                <label for="discount">Recurring Customer Discount (%):</label>
                <input type="number" id="discount" name="discount_percent" 
                       value="<?= $settings['discount_percent']; ?>" min="0" max="100">
            </div>
        </section>

        <section class="admin-section">
            <h2>Room Prices</h2>
            <div class="grid-3">
                <?php foreach ($rooms as $room): ?>
                    <div class="room-card">
                        <h4><?= $room['type']; ?></h4>
                        <label>Price per night ($):</label>
                        <input type="number" 
                               name="room_prices[<?= $room['id']; ?>]" 
                               value="<?= $room['price']; ?>" 
                               step="0.01">
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="admin-section">
            <h2>Manage Features</h2>
            <p class="peragraph">Uncheck a box to disable the feature on the booking page.</p>
            <div class="features-list">
                <?php foreach ($features as $feature): ?>
                    <div class="feature-item">
                        <input type="checkbox" 
                               id="feat_<?= $feature['id']; ?>" 
                               name="active_features[]" 
                               value="<?= $feature['id']; ?>"
                               <?= $feature['active'] ? 'checked' : ''; ?>>
                        <label for="feat_<?= $feature['id']; ?>">
                            <?= $feature['name']; ?> ($<?= $feature['price']; ?>)
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <button type="submit" class="save-btn">Save Changes</button>
    </form>
</main>

<style>
    .admin-container { max-width: 800px; margin: 2rem auto; padding: 1rem; }
    .admin-section { background: #f4f4f4; padding: 1.5rem; margin-bottom: 2rem; border-radius: 8px; }
    .grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; }
    .room-card { background: white; padding: 1rem; border-radius: 4px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
    .features-list { display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.5rem; }
    .save-btn { background: #2c3e50; color: white; padding: 1rem 2rem; border: none; cursor: pointer; font-size: 1.1rem; }
    .alert { background: #d4edda; color: #155724; padding: 1rem; margin-bottom: 1rem; border: 1px solid #c3e6cb; }
    .peragraph { color: var(--nav-color); }
</style>

<?php require (__DIR__ . '/../../views/footer.php'); ?>