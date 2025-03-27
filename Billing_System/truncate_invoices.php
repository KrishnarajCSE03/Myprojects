<?php
include 'config/db.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error_log.txt');

// Set JSON response header
header('Content-Type: application/json');

$response = ['status' => '', 'message' => ''];

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // Disable foreign key checks
        $conn->query("SET FOREIGN_KEY_CHECKS = 0");

        // Truncate related tables first to avoid foreign key constraint issues
        $tables = ['invoice_items', 'payments', 'transpositions', 'accounts', 'invoices'];

        foreach ($tables as $table) {
            if (!$conn->query("TRUNCATE TABLE $table")) {
                throw new Exception("Failed to truncate the $table table.");
            }
        }

        // Re-enable foreign key checks
        $conn->query("SET FOREIGN_KEY_CHECKS = 1");

        $response = ['status' => 'success', 'message' => 'Accounts Cleared, New Year Accounts Created !!!!.'];

    } else {
        throw new Exception('Invalid request method.');
    }

} catch (Exception $e) {
    $response = ['status' => 'error', 'message' => $e->getMessage()];
}

echo json_encode($response);
?>
