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

    if (!$room) {
        throw new Exception("Invalid room selected.");
    }

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
    $featuresForReceipt = [];

    if (!empty($selectedFeatures)) {
        $selectedFeatures = array_map('intval', $selectedFeatures);
        $placeholders = implode(',', array_fill(0, count($selectedFeatures), '?'));

        $stmtFeatures = $database->prepare("
            SELECT f.name, f.price, a.name AS activity_name, f.tier_name
            FROM features f
            JOIN activities a ON f.activity_id = a.id
            WHERE f.id IN ($placeholders)
        ");
        
        $stmtFeatures->execute($selectedFeatures);
        $featuresDB = $stmtFeatures->fetchAll(PDO::FETCH_ASSOC);

        $featuresForReceipt = [];

        foreach ($featuresDB as $feat) {
            $featuresCost += (int)$feat['price'];
            $featuresDetails[] = [
                "name" => $feat['name'],
                "cost" => (int)$feat['price']
            ];

            $featuresForReceipt[] = [
                "activity" => strtolower(trim($feat['activity_name'])),
                "tier" => strtolower(trim($feat['tier_name']))  
            ];
        }

    }

    $totalCost = $roomCost + $featuresCost;

    // Check for returning customer
    $stmtCheckLoyalty = $database->prepare("SELECT COUNT(*) FROM bookings WHERE user_name = :user_name");
    $stmtCheckLoyalty->execute([':user_name' => $userName]);
    $previousBookings = $stmtCheckLoyalty->fetchColumn();

    $stmtDiscount = $database->query("SELECT discount_percent FROM settings WHERE id = 1");
    $discountPercent = $stmtDiscount->fetchColumn(); // e.g., 10
    
    $discount = 0;
    if ($previousBookings > 0) {
        $discount = $totalCost * ($discountPercent / 100);
        $totalCost -= $discount;
    }

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
    try {
        $stmtInsert = $database->prepare("
            INSERT INTO bookings (room_id, user_name, arrival_date, departure_date, total_cost, transfer_code, discount_amount)
            VALUES (:room_id, :user_name, :arrival_date, :departure_date, :total_cost, :transfer_code, :discount_amount)
        ");

        $result = $stmtInsert->execute([
            ':room_id' => $roomId,
            ':user_name' => $userName,
            ':arrival_date' => $arrivalDate,
            ':departure_date' => $departureDate,
            ':total_cost' => $totalCost,
            ':transfer_code' => $transferCode,
            ':discount_amount' => $discount
        ]);

        if (!$result) {
            throw new Exception("Failed to insert booking into database");
        }

        $bookingId = $database->lastInsertId();

        if (!$bookingId) {
            throw new Exception("Could not retrieve booking ID from database");
        }

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

    } catch (PDOException $e) {
        // Database error occurred
        error_log("Database error during booking insertion: " . $e->getMessage());
        echo json_encode(['error' => 'Database error: Failed to save booking. ' . $e->getMessage()]);
        exit;
    } catch (Exception $e) {
        // Other error occurred
        error_log("Error during booking insertion: " . $e->getMessage());
        echo json_encode(['error' => 'Error saving booking: ' . $e->getMessage()]);
        exit;
    }

    $receiptStatus = "Not sent yet";

    try {
        $receiptResponse = $client->request('POST', 'https://www.yrgopelag.se/centralbank/receipt', [
            'json' => [
                'user' => $hotelUserName,
                'api_key' => $apiKey,
                'guest_name' => $userName,
                'arrival_date' => $arrivalDate,
                'departure_date' => $departureDate,
                'features_used' => $featuresForReceipt ?? [],
                'star_rating' => (int)$stars
            ]
        ]);

        $receiptData = json_decode($receiptResponse->getBody()->getContents(), true);
        $receiptStatus = "Sent to Bank";
        
    // } catch (ClientException $e) {
    //     // This catches 400 errors (Bad Request) from the bank
    //     // It will print the EXACT reason the bank rejected it
    //     $receiptStatus = "BANK ERROR: " . $e->getResponse()->getBody()->getContents();
        
    } catch (Exception $e) {
        // This catches other connection errors
        $receiptStatus = "CONNECTION ERROR: " . $e->getMessage();
    }

    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Booking and Receipt confirmed!',
        'bookingId' => $bookingId,
        'totalCost' => $totalCost,
        'star_rating' => $stars,
        'discountApplied' => $discount > 0 ? "You saved $$discount!" : "No discount applied",
        'features' => $featuresForReceipt,
        'receipt_status' => $receiptStatus
    ]);

} catch (Exception $e) {
    // Handle any other exceptions from the outer try block
    echo json_encode(['error' => $e->getMessage()]);
    exit;
}
