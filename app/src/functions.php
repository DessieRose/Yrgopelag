<?php
declare(strict_types=1);

if (isset($pdo)) {
    $pdo = $pdo;
} else {
    // Fallback if your autoload doesn't create the variable automatically
    // $pdo = new PDO('sqlite:your_db_path.db');
}
function getRoomAvailability(\PDO $pdo, int $roomId): array
{
    // Fetch the room name
    $stmtRoom = $pdo->prepare("SELECT * FROM rooms WHERE id = :id");
    $stmtRoom->bindParam(':id', $roomId, PDO::PARAM_INT);
    $stmtRoom->execute();

    // Fetch all bookings for the room in January 2026
    $sql = "SELECT arrival_date, departure_date FROM bookings 
            WHERE room_id = :room_id 
            AND arrival_date BETWEEN '2026-01-01' AND '2026-01-31'
            AND departure_date BETWEEN '2026-01-01' AND '2026-02-01'"; // Check out up to Feb 1st
            
    $stmtBookings = $pdo->prepare($sql);
    $stmtBookings->bindParam(':room_id', $roomId, PDO::PARAM_INT);
    $stmtBookings->execute();
    $bookings = $stmtBookings->fetchAll(PDO::FETCH_ASSOC);

    $bookedDates = [];
    
    // Populate the array of booked day numbers (1 to 31)
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
        'booked_dates' => array_unique($bookedDates)
    ];
}


function isWeekend(int $day): bool
{
    $dayOfWeek = ($day + 3) % 7; // (Day 1 + 3) % 7 = 4 (Thursday)
    return $dayOfWeek === 6 || $dayOfWeek === 0;
}


function calculateTotalCost(string $arrivalDate, string $departureDate, float $nightlyRate): float
{
    $arrival = new DateTime($arrivalDate);
    $departure = new DateTime($departureDate);
    $interval = $arrival->diff($departure);
    $nights = (int)$interval->format('%a');

    return $nights * $nightlyRate;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/../../vendor/autoload.php';
    require_once __DIR__ . '/autoload.php';
    header('Content-Type: application/json');

    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    try {
        $dbName = 'hotel.db'; // CHECK THIS NAME
        $dbPath = __DIR__ . '/../database/' . $dbName; // Adjust path to where your .db file is
        $pdo = new PDO("sqlite:$dbPath");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        // Send a JSON error back if connection fails
        echo json_encode(['success' => false, 'error' => 'DB Connection failed']);
        exit;
    }

    // 2. Process the JSON input
    $header = $_SERVER['CONTENT_TYPE'] ?? '';
    if (strpos($header, 'application/json') !== false) {
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);

        if (($data['action'] ?? '') === 'calculateTotalCost') {
            $roomId = (int)$data['room_id'];
            $arrivalDate = $data['arrival_date'];
            $departureDate = $data['departure_date'];

            // Fetch room price using the local $pdo connection
            $stmt = $pdo->prepare("SELECT price FROM rooms WHERE id = :id");
            $stmt->execute(['id' => $roomId]);
            $room = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$room) {
                echo json_encode(['success' => false, 'error' => 'Room not found']);
                exit;
            }

            $nightlyRate = (float)$room['price'];

            try {
                $arrival = new DateTime($arrivalDate);
                $departure = new DateTime($departureDate);
                
                if ($arrival >= $departure) {
                    echo json_encode(['success' => false, 'error' => 'Check-out must be after check-in']);
                    exit;
                }

                $interval = $arrival->diff($departure);
                $nights = (int)$interval->format('%a');
                $totalCost = $nights * $nightlyRate;

                // Send clean JSON back
                echo json_encode([
                    'success' => true,
                    'nightlyRate' => $nightlyRate,
                    'nights' => $nights,
                    'totalCost' => $totalCost
                ]);
                exit;

            } catch (Exception $e) {
                echo json_encode(['success' => false, 'error' => 'Invalid dates']);
                exit;
            }
        }
    }
}