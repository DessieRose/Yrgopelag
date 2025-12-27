<?php 
require (__DIR__ . '/autoload.php');
require (__DIR__ . '/../../views/header.php'); 

$stmtRooms = $database->query("SELECT * FROM rooms");
$rooms = $stmtRooms->fetchAll(PDO::FETCH_ASSOC);

$stmtFeatures = $database->query("SELECT * FROM features");
$features = $stmtFeatures->fetchAll(PDO::FETCH_ASSOC);

$stmtHotelFeatures = $database->query("SELECT * FROM hotel_features");
$hotelFeatures = $stmtHotelFeatures->fetchAll(PDO::FETCH_ASSOC);

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
                        <option value="<?= $room['id']; ?>" <?= $room['id'] == $selectedRoomId ? 'selected' : ''; ?>>
                            <?= $room['type']; ?> ($<?= $room['price']; ?>/night)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="date-inputs">
                <div class="form-group">
                    <label for="arrival-<?= $roomId; ?>">Arrival Date (15:00)</label>
                    <input type="date" id="arrival-<?= $roomId; ?>"name="arrival_date" required min="2026-01-01" max="2026-01-30">
                </div>

                <div class="form-group">
                    <label for="departure-<?= $roomId; ?>">Departure Date (11:00)</label>
                    <input type="date" id="departure-<?= $roomId; ?>" name="departure_date" required min="2026-01-02" max="2026-01-31"> </div>
                </div>
            </div>

            <div class="features-selection">
                <h3>Extra Features:</h3>
                <?php foreach ($features as $feature): 
                    foreach ($hotelFeatures as $hotelFeture):
                    ?>
                    
                    <div class="feature-item">
                        <input type="checkbox" name="features[]" id="feat-<?= $feature['id']; ?>" value="<?= $feature['id']; ?>">
                        <label for="feat-<?= $feature['id']; ?>">
                            <?= $hotelFeature['name']; ?> (+$<?= $hotelFeture['price']; ?>)
                        </label>
                    </div>
                    <?php endforeach; ?>
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
           
        <!-- <input type="hidden" name="room_id" value="<?= $roomId; ?>">
        <input type="hidden" name="arrival_date" value="<?= $arrival; ?>">
        <input type="hidden" name="departure_date" value="<?= $departure; ?>">
        <input type="hidden" name="room_price" id="room-price" value="<?= $room['price']; ?>">

        <h3>Add Extra Features:</h3>
        <?php foreach ($features as $feature): ?>
            <div class="feature-item">
                <input type="checkbox" name="features[]" value="<?= $feature['id']; ?>" 
                    data-price="<?= $feature['price']; ?>" class="feature-checkbox">
                <label><?= $feature['name']; ?> ($<?= $feature['price']; ?>)</label>
            </div>
        <?php endforeach; ?>

        <div class="summary">
            <label for="transfer_code">Transfer Code:</label>
            <input type="text" name="transfer_code" required>
            
            <p>Total Price: <span id="display-total">$0</span></p>
            <input type="hidden" name="total_price" id="input-total" value="0">
        </div>

        <button type="submit">Confirm & Pay</button>
    </form>
</main>
 -->

    <!-- // old code below
    <form action="/app/src/booking.php" method="POST" class="booking-form">
        <input type="hidden" name="room_id" value="<?= $roomId; ?>">
        
        <div class="date-inputs">
            <label for="arrival-<?= $roomId; ?>">Arrival Date (15:00)</label>
            <input type="date" 
                   id="arrival-<?= $roomId; ?>" 
                   name="arrival_date" 
                   required
                   min="2026-01-01" 
                   max="2026-01-30">

            <label for="departure-<?= $roomId; ?>">Departure Date (11:00)</label>
            <input type="date" 
                   id="departure-<?= $roomId; ?>" 
                   name="departure_date" 
                   required
                   min="2026-01-02" 
                   max="2026-02-01"> </div>

        <div class="total-cost"><strong>Total: $0</strong></div>
        <button type="submit">Book hotel</button>
    </form> -->

<?php require (__DIR__ . '/../../views/footer.php'); ?>