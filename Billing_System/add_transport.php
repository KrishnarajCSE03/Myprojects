<?php
session_start();
include 'config/db.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["error" => "Invalid request method."]);
    exit();
}

$invoice_id = $_POST['invoice_id'];
$customer_id = $_POST['customer_id'];
$from_location = $_POST['from_location'];
$destination = $_POST['destination'];
$transport_through = $_POST['transport_through'];
$transport_company = $_POST['transport_company'];
$tracking_number = $_POST['tracking_number'];

$stmt = $conn->prepare("INSERT INTO transpositions 
(invoice_id, customer_id, from_location, destination, transport_through, transport_company, tracking_number) 
VALUES (?, ?, ?, ?, ?, ?, ?)");

$stmt->bind_param("iisssss", $invoice_id, $customer_id, $from_location, $destination, $transport_through, $transport_company, $tracking_number);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["error" => "Failed to save transposition details."]);
}
?>
