<?php

declare(strict_types=1);

// Define the root path of the project.
define('ROOT_PATH', realpath(__DIR__ . '/../../'));

// Set the default timezone to Coordinated Universal Time.
date_default_timezone_set('UTC');

// Set the default character encoding to UTF-8.
mb_internal_encoding('UTF-8');

// Load environment variables from .env file
require_once __DIR__ . '/../../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../'); 
$dotenv->load();

// Include the helper functions.
require_once __DIR__ . '/functions.php';

// Fetch the global configuration array.
$config = require __DIR__ . '/config.php';

// Setup the database connection.
$database = new PDO($config['database_path']);

// Enable error mode so exceptions are thrown
$database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Set up error handling for the database
try {
    // Test the connection
    $database->query("SELECT 1");
} catch (PDOException $e) {
    error_log("Database connection failed: " . $e->getMessage());
    die("Database connection error. Please check the database configuration.");
}
