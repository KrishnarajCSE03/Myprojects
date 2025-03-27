<?php
session_start();
include 'config/db.php';

// Set header for JSON response
header('Content-Type: application/json');

// ✅ Validate Session
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit();
}

// ✅ Validate Inputs
$invoiceId = isset($_POST['invoice_id']) ? intval($_POST['invoice_id']) : 0;
$status = isset($_POST['status']) ? $_POST['status'] : '';

if (!$invoiceId || !in_array($status, ['pending', 'paid'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid invoice ID or status.']);
    exit();
}

// ✅ Update the Status in Database
$stmt = $conn->prepare("UPDATE invoices SET status = ? WHERE invoice_id = ?");
$stmt->bind_param("si", $status, $invoiceId);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Payment status updated successfully!']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update status. Please try again.']);
}

$stmt->close();
$conn->close();
?>
