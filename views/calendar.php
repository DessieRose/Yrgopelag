<?php
declare(strict_types=1);

// Fetch availability data
$availability = getRoomAvailability($database, $roomId); 
// $roomName = $availability['room_name'];
$bookedDays = $availability['booked_dates'];

// Ensure room ID is available to JavaScript
?>

<div class="room-booking-block" data-room-id="<?= $roomId; ?>">
    <h3>Availability - January 2026</h3>
    
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
                   max="2026-01-31"> </div>

<<<<<<< Updated upstream
        <button type="submit">Check Price & Book</button>
=======
        <div class="total-cost"><strong>Total:</strong></div>
        <button type="submit">Book</button>
>>>>>>> Stashed changes
    </form>
    
    <section class="calendar">
        <div class="day-label">Mon</div>
        <div class="day-label">Tue</div>
        <div class="day-label">Wed</div>
        <div class="day-label">Thu</div>
        <div class="day-label">Fri</div>
        <div class="day-label">Sat</div>
        <div class="day-label">Sun</div>

        <div class="day empty"></div>
        <div class="day empty"></div>
        <div class="day empty"></div>

        <?php for ($day = 1; $day <= 31; $day++):
            $isBooked = in_array($day, $bookedDays);
            $isWeekend = isWeekend($day);
            
            $classes = "day";
            if ($isBooked) {
                $classes .= " booked";
            }
            if ($isWeekend) {
                $classes .= " weekend";
            }
            
            $dayOfMonth = str_pad((string)$day, 2, '0', STR_PAD_LEFT);
            $dataDate = "2026-01-{$dayOfMonth}";
            ?>
            
            <div class="<?= $classes; ?>" title="<?= $isBooked ? 'BOOKED' : 'Available'; ?>">
                <?= $day; ?>
            </div>
        <?php endfor; ?>
    </section>
</div>