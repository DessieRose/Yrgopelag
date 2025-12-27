<?php

declare(strict_types=1);
require (__DIR__ . '/../../vendor/autoload.php');
require __DIR__ . '/autoloade.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

header('Content-Type: application/json');

// Ensure the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// CONFIGURATION: Set your Hotel's Name and API Key here
$hotelUserName = 'The Volcano Resort'; // Your "user" in the docs
$apiKey = 'your-api-key-here';     // The API key provided by the school

try {
    // collect and sanitize input
    $userName = htmlspecialchars($_POST['user_name'] ?? '');
    $transferCode = htmlspecialchars($_POST['transfer_code'] ?? '');
    $roomId = (int)$_POST['room_id'] ?? 0;
    $arrivalDate = $_POST['arrival_date'] ?? '';
    $departureDate = $_POST['departure_date'] ?? '';
    $selectedFeatures = $_POST['features'] ?? [];

    if (!$roomId || !$arrivalDate || !$departureDate || !$transferCode || !$userName) {
        throw new Exception("Missing required fields.");
    }

    // Calculate total cost
    $stmt = $database->prepare("
            SELECT id FROM bookings 
            WHERE room_id = :room_id 
            AND (
                (arrival_date < :departure_date) AND 
                (departure_date > :arrival_date)
            )
        ");

    $stmt->execute([
        ':room_id' => $roomId,
        ':arrival_date' => $arrivalDate,
        ':departure_date' => $departureDate
    ]);

    if ($stmt->fetch()) {
        throw new Exception("Room is already booked for these dates.");
    }

    $stmtRoom = $database->prepare("SELECT room_number, type, price FROM rooms WHERE id = :id");
    $stmtRoom->execute([':id' => $roomId]);
    $room = $stmtRoom->fetch(PDO::FETCH_ASSOC);

    $stmtSettings = $database->query("SELECT hotel_stars FROM settings WHERE id = 1");
    $stars = $stmtSettings->fetchColumn();

    // Calculate Nights
    $arrival = new DateTime($arrivalDate);
    $departure = new DateTime($departureDate);

    if ($arrival >= $departure) {
        throw new Exception("Departure date must be after arrival date.");
    }

    $days = $arrival->diff($departure)->days;
    $roomCost = $room['price'] * $days;

    // Fetch and Calculate Features Cost
    $featuresCost = 0;
    $featuresDetails = [];

    if (!empty($featureIds)) {
        // Create placeholders for SQL
        $placeholders = implode(',', array_fill(0, count($featureIds), '?'));
        $stmtFeatures = $database->prepare('SELECT id, name, tier_name, price, active FROM features WHERE id IN (' . $placeholders . ')');
        $stmtFeatures->execute($featureIds);
        $featuresDB = $stmtFeatures->fetchAll(PDO::FETCH_ASSOC);

        foreach ($featuresDB as $feat) {
            $featuresCost += $feat['price'];
            $featuresDetails[] = [
                "name" => $feat['name'],
                "cost" => $feat['price']
            ];
        }
    }

    $totalCost = $roomCost + $featuresCost;

    $client = new Client();

    $response = $client->post('https://www.yrgopelag.se/centralbank/transferCode', [
        'json' => [
            'transferCode' => $transferCode,
            'totalCost' => $totalCost
        ]
    ]);

    $transferCheck = json_decode($response->getBody()->getContents(), true);

    if (isset($transferCheck['error'])) {
        throw new Exception("Invalid Transfer Code: " . $transferCheck['error']);
    }

    $receiptResponse = $client->post('https://www.yrgopelag.se/centralbank/receipt', [
        'json' => [
            "user" => $hotelUserName,
            "api_key" => $apiKey,
            "guest_name" => $userName,
            "arrival_date" => $arrivalDate,
            "departure_date" => $departureDate,
            "room_price" => $room['price'],
            "total_cost" => $totalCost,
            "features_used" => $featuresDetails, // Sending our array of selected features
            "star_rating" => (int)$stars,
        ]
    ]);

    $receiptData = json_decode($receiptResponse->getBody()->getContents(), true);

    $database->beginTransaction();

    $insertBooking = $database->prepare("
        INSERT INTO bookings (room_id, user_name, arrival_date, departure_date, total_cost, transfer_code)
        VALUES (:room_id, :user_name, :arrival_date, :departure_date, :total_cost, :transfer_code)
    ");

    $insertBooking->execute([
        ':room_id' => $roomId,
        ':user_name' => $userName,
        ':arrival_date' => $arrivalDate,
        ':departure_date' => $departureDate,
        ':total_cost' => $totalCost,
        ':transfer_code' => $transferCode
    ]);

    $bookingId = $database->lastInsertId();

    if (!empty($featureIds)) {
        $insertFeature = $database->prepare("INSERT INTO booking_features (booking_id, hotel_feature_id) VALUES (?, ?)");
        foreach ($featureIds as $featId) {
            $insertFeature->execute([$bookingId, $featId]);
        }
    }

    $database->commit();

    $depositResponse = $client->post('https://www.yrgopelag.se/centralbank/deposit', [
        'json' => [
            "user" => $hotelUserName,
            "transferCode" => $transferCode
        ]
    ]);

    echo json_encode([
        "success" => true,
        "message" => "Booking confirmed!",
        "booking_details" => [
            "island" => "Volcano island",
            "hotel" => $hotelName,
            "arrival_date" => $arrivalDate,
            "departure_date" => $departureDate,
            "total_cost" => $totalCost,
            "stars" => (int)$stars,
            "features" => $featuresDetails,
            "additional_info" => "Thank you for choosing $hotelName."
        ]
    ]);

} catch (ClientException $e) {
    // Catch Guzzle errors (4xx or 5xx from Central Bank)
    if ($database->inTransaction()) {
        $database->rollBack();
    }
    $response = $e->getResponse();
    $responseBodyAsString = $response->getBody()->getContents();
    echo json_encode(['error' => 'Central Bank Error: ' . $responseBodyAsString]);

} catch (Exception $e) {
    // Catch general PHP errors
    if ($database->inTransaction()) {
        $database->rollBack();
    }
    echo json_encode(['error' => $e->getMessage()]);
}