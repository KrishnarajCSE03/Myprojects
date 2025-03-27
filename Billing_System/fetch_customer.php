<?php
include 'config/db.php';

// Ensure it's a POST request
if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST['mobile'])) {
    echo json_encode(["success" => false, "message" => "Invalid request."]);
    exit();
}

$mobile = trim($_POST['mobile']);

// Validate mobile number
if (empty($mobile)) {
    echo json_encode(["success" => false, "message" => "Mobile number is required."]);
    exit();
}

// Fetch customer details
$stmt = $conn->prepare("SELECT customer_id, name, gstn, address, pincode, district, state FROM customers WHERE phone = ?");
$stmt->bind_param("s", $mobile);
$stmt->execute();
$result = $stmt->get_result();

// If customer found, return details
if ($row = $result->fetch_assoc()) {
    echo json_encode([
        "success"     => true,
        "customer_id" => $row['customer_id'],
        "name"        => $row['name'],
        "gstn"        => $row['gstn'] ?: "Not Available",
        "address"     => $row['address'],
        "pincode"     => $row['pincode'],
        "district"    => $row['district'],
        "state"       => $row['state']
    ]);
} else {
    echo json_encode(["success" => false, "message" => "Customer not found."]);
}
?>
