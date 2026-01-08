<?php
declare(strict_types=1);

// Fetch availability data
$availability = getRoomAvailability($database, $roomId); 
// $roomName = $availability['room_name'];
$bookedDays = $availability['booked_dates'];

// Ensure room ID is available to JavaScript
?>

<div class="room-booking-block" data-room-id="<?= $roomId; ?>">
    
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