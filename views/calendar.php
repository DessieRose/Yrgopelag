<?php

// Fetch availability data
$availability = getRoomAvailability($database, $roomId); 
// $roomName = $availability['room_name'];
$bookedDays = $availability['booked_dates'];

// Ensure room ID is available to JavaScript
?>

<div class="room-calendar" data-room-id="<?= $roomId; ?>">
    <h3>Availability - January 2026</h3>

    <div class="selected-dates-info">
        <span class="check-in-display">Check-in: --</span> | 
        <span class="check-out-display">Check-out: --</span>
    </div>

    <section class="calendar">
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
            // Add data attributes for JS to read
            $dataAttributes = "data-day='2026-01-{$day}'"; 
            if ($isBooked) {
                $dataAttributes .= " data-booked='true'";
            }
            ?>
            
            <div class="<?= $classes; ?>" <?= $dataAttributes; ?>>
                <?= $day; ?>
            </div>
        <?php endfor; ?>
    </section>
</div>

