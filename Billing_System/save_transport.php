<?php
include 'config/db.php'; // Include DB connection

header('Content-Type: application/json');

// ✅ Log received data for debugging
file_put_contents("debug_log.txt", "Received POST Data: " . json_encode($_POST, JSON_PRETTY_PRINT) . "\n", FILE_APPEND);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $invoiceId = isset($_POST['invoice_id']) ? intval($_POST['invoice_id']) : null;
    $customerId = isset($_POST['customer_id']) ? intval($_POST['customer_id']) : null;
    $fromLocation = trim($_POST['from_location'] ?? '');
    $destination = trim($_POST['destination'] ?? '');
    $transportThrough = trim($_POST['transport_through'] ?? '');
    $transportCompany = trim($_POST['transport_company'] ?? '');
    $trackingNumber = trim($_POST['tracking_number'] ?? '');
    $status = trim($_POST['status'] ?? 'Pending');
    $createdAt = date("Y-m-d H:i:s");
    $updatedAt = $createdAt;

    // ✅ Check for missing fields
    $missingFields = [];
    if (!$invoiceId) $missingFields[] = "invoice_id";
    if (!$customerId) $missingFields[] = "customer_id";
    if (empty($fromLocation)) $missingFields[] = "from_location";
    if (empty($destination)) $missingFields[] = "destination";
    if (empty($transportThrough)) $missingFields[] = "transport_through";
    if (empty($status)) $missingFields[] = "status";

    if (!empty($missingFields)) {
        file_put_contents("debug_log.txt", "Missing Fields: " . implode(", ", $missingFields) . "\n", FILE_APPEND);
        echo json_encode(['success' => false, 'message' => 'All required fields must be filled.', 'missing_fields' => $missingFields]);
        exit;
    }

    // ✅ Validate ENUM values
    $validTransportTypes = ['Lorry', 'Courier', 'Hand Delivery'];
    $validStatuses = ['Pending', 'In Transit', 'Delivered', 'Canceled'];

    if (!in_array($transportThrough, $validTransportTypes)) {
        echo json_encode(['success' => false, 'message' => 'Invalid transport type.']);
        exit;
    }

    if (!in_array($status, $validStatuses)) {
        echo json_encode(['success' => false, 'message' => 'Invalid status.']);
        exit;
    }

    // ✅ Insert into database
    $stmt = $conn->prepare("INSERT INTO transpositions 
        (invoice_id, customer_id, from_location, destination, transport_through, transport_company, tracking_number, status, created_at, updated_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param("iissssssss", 
        $invoiceId, $customerId, $fromLocation, $destination, 
        $transportThrough, $transportCompany, $trackingNumber, 
        $status, $createdAt, $updatedAt);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Transport details saved successfully.']);
    } else {
        file_put_contents("debug_log.txt", "SQL Error: " . $stmt->error . "\n", FILE_APPEND);
        echo json_encode(['success' => false, 'message' => 'Failed to save transport details.']);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
