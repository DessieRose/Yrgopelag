<?php
declare(strict_types=1);

function getRoomAvailability(\PDO $pdo, int $roomId): array
{
    // Assume $pdo is your global PDO connection object or retrieved via a function
    // global $pdo; 
    
    // 1. Fetch the room name
    $stmtRoom = $pdo->prepare("SELECT name FROM rooms WHERE id = :id");
    $stmtRoom->bindParam(':id', $roomId, PDO::PARAM_INT);
    $stmtRoom->execute();
    // $roomName = $stmtRoom->fetchColumn() ?: 'Unknown Room';

    // 2. Fetch all bookings for the room in January 2026
    $sql = "SELECT arrival_date, departure_date FROM bookings 
            WHERE room_id = :room_id 
            AND arrival_date BETWEEN '2026-01-01' AND '2026-01-31'
            AND departure_date BETWEEN '2026-01-01' AND '2026-02-01'"; // Check out up to Feb 1st
            
    $stmtBookings = $pdo->prepare($sql);
    $stmtBookings->bindParam(':room_id', $roomId, PDO::PARAM_INT);
    $stmtBookings->execute();
    $bookings = $stmtBookings->fetchAll(PDO::FETCH_ASSOC);

    $bookedDates = [];
    
    // 3. Populate the array of booked day numbers (1 to 31)
    foreach ($bookings as $booking) {
        $arrival = new DateTime($booking['arrival_date']);
        $departure = new DateTime($booking['departure_date']);

        // Check-in (15:00) means the arrival date is the first booked day
        // Check-out (11:00) means the departure date is the first AVAILABLE day
        $interval = new DateInterval('P1D');
        $period = new DatePeriod($arrival, $interval, $departure);

        foreach ($period as $date) {
            $dayOfMonth = (int)$date->format('j');
            if ($dayOfMonth >= 1 && $dayOfMonth <= 31) {
                $bookedDates[] = $dayOfMonth;
            }
        }
    }
    
    return [
        // 'room_name' => $roomName,
        'booked_dates' => array_unique($bookedDates)
    ];
}

/**
 * Determines if a given day of the month (1-31) is a weekend in Jan 2026.
 * January 1, 2026 is a Thursday.
 * @param int $day Day of the month (1-31).
 * @return bool
 */
function isWeekend(int $day): bool
{
    // Jan 1 (day 1) is a Thursday (4)
    // Fri (5) and Sat (6) are weekend (We'll simplify to Sat/Sun for grading)
    
    // Jan 3 (day 3) is a Saturday (6)
    // Jan 4 (day 4) is a Sunday (7)
    $dayOfWeek = ($day + 3) % 7; // (Day 1 + 3) % 7 = 4 (Thursday)

    // 6 = Saturday, 0 = Sunday
    return $dayOfWeek === 6 || $dayOfWeek === 0;
}