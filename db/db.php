<?php
/**
 * Database Connection
 * Uses configuration from config/config.php
 */

require_once __DIR__ . '/../config/config.php';

try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Database connection failed");
    }

    // Set charset to utf8mb4 for proper encoding
    if (!$conn->set_charset("utf8mb4")) {
        throw new Exception("Error loading character set utf8mb4: " . $conn->error);
    }

} catch (Exception $e) {
    // Log the error to a file
    error_log("Database Error: " . $e->getMessage() . " at " . date('Y-m-d H:i:s'));

    // Redirect to error page
    header("Location: ../page/error.php?code=db_connection");
    exit;
}