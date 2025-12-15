<?php
require __DIR__ . '/autoloade.php';
?>

<!-- <label for="arrival">Arrival:</label>
<input type="date" id="arrival" name="arrival" 
       min="2026-01-01" max="2026-01-31">

<label for="departure">Departure:</label>
<input type="date" id="departure" name="departure" 
       min="2026-01-01" max="2026-01-31"> -->

<?php

$year = 2026;
$month = 1;

// Fetch bookings overlapping January 2026
$statment = $pdo->prepare("
  SELECT arrival_date, departure_date
  FROM bookings
  WHERE arrival_date < :monthEnd
    AND departure_date > :monthStart
");

$statment->execute([
  ':monthStart' => '2026-01-01',
  ':monthEnd'   => '2026-02-01',
]);

$bookings = $statment->fetchAll();

// Build set of blocked dates
$blockedDates = [];

foreach ($bookings as $booking) {
    $start = new DateTime($booking['arrival_date']);
    $end = new DateTime($booking['departure_date']);

    // Checkout day is NOT blocked
    $end->modify('-1 day');

    while ($start <= $end) {
        $blockedDates[$start->format('Y-m-d')] = true;
        $start->modify('+1 day');
    }
}

$firstDay = new DateTime('2026-01-01');
$daysInMonth = (int)$firstDay->format('t');
$startWeekday = (int)$firstDay->format('N'); // 1 (Mon) - 7 (Sun)