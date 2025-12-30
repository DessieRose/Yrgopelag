<?php

declare(strict_types=1);
require (__DIR__ . '/../../vendor/autoload.php');
require __DIR__ . '/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;

header('Content-Type: application/json');

// Ensure the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// CONFIGURATION: Set your Hotel's Name and API Key here
$hotelUserName = $_ENV['ISLAND_USER'];
$apiKey = $_ENV['API_KEY'];

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

    if (!empty($selectedFeatures)) {
        $selectedFeatures = array_map('intval', $selectedFeatures);
        
        // Create placeholders for SQL
        $placeholders = implode(',', array_fill(0, count($selectedFeatures), '?'));
        
        $stmtFeatures = $database->prepare('SELECT id, name, price FROM features WHERE id IN (' . $placeholders . ')');
        $stmtFeatures->execute($selectedFeatures);
        $featuresDB = $stmtFeatures->fetchAll(PDO::FETCH_ASSOC);

        foreach ($featuresDB as $feat) {
            $featuresCost += (int)$feat['price'];
            $featuresDetails[] = [
                "name" => $feat['name'],
                "cost" => (int)$feat['price']
            ];
        }
    }

    $totalCost = $roomCost + $featuresCost;

    $client = new Client();

    try {
        $response = $client->request('POST', 'https://www.yrgopelag.se/centralbank/deposit', [
            'form_params' => [
                'user' => $hotelUserName,
                'transferCode' => $transferCode,
                'amount' => $totalCost
            ]
        ]);
        
        // If we got here, the bank said OK! 
        $responseBody = json_decode($response->getBody()->getContents(), true);
    
    } catch (ClientException $e) {
        // 4xx Errors (User error: bad code, wrong amount)
        echo json_encode(['error' => 'Bank declined: ' . $e->getResponse()->getBody()->getContents()]);
        exit;
    } catch (ServerException $e) {
        // 5xx Errors (Bank server error)
        echo json_encode(['error' => 'Bank server error. Try again later or check your transfer code value.']);
        exit;
    }

    // If payment was successful, insert the booking into the database
    $stmtInsert = $database->prepare("
        INSERT INTO bookings (room_id, user_name, arrival_date, departure_date, total_cost, transfer_code)
        VALUES (:room_id, :user_name, :arrival_date, :departure_date, :total_cost, :transfer_code)
    ");

    $stmtInsert->execute([
        ':room_id' => $roomId,
        ':user_name' => $userName,
        ':arrival_date' => $arrivalDate,
        ':departure_date' => $departureDate,
        ':total_cost' => $totalCost,
        ':transfer_code' => $transferCode
    ]);

    $bookingId = $database->lastInsertId();

    // Insert booking features if any
    if (!empty($selectedFeatures)) {
        $stmtBookingFeatures = $database->prepare("
            INSERT INTO booking_features (booking_id, feature_id)
            VALUES (:booking_id, :feature_id)
        ");

        foreach ($selectedFeatures as $featureId) {
            $stmtBookingFeatures->execute([
                ':booking_id' => $bookingId,
                ':feature_id' => (int)$featureId
            ]);
        }
    }

    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Booking confirmed!',
        'bookingId' => $bookingId,
        'totalCost' => $totalCost
    ]);

} catch (Exception $e) {
    // Handle any other exceptions from the outer try block
    echo json_encode(['error' => $e->getMessage()]);
    exit;
}