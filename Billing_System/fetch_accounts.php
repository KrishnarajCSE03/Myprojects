<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');
include 'config/db.php';

// Validate date parameters
$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';

if (empty($from) || empty($to)) {
    echo json_encode(["error" => "Date range is required."]);
    exit();
}

// Prepare the SQL statement
$stmt = $conn->prepare("
    SELECT 
        invoices.invoice_id, 
        customers.name AS customer, 
        invoices.created_at AS date, 
        invoices.total_amount, 
        invoices.tax, 
        invoices.discount, 
        invoices.final_amount
    FROM invoices
    INNER JOIN customers ON invoices.customer_id = customers.customer_id
    WHERE invoices.created_at BETWEEN ? AND ?
");

if (!$stmt) {
    echo json_encode(["error" => "Database Error: " . $conn->error]);
    exit();
}

$stmt->bind_param("ss", $from, $to);
$stmt->execute();
$result = $stmt->get_result();

$sales = [];
$totalSales = $totalTax = $totalDiscount = $finalAmount = 0;

while ($row = $result->fetch_assoc()) {
    $sales[] = $row;
    $totalSales += $row['total_amount'];
    $totalTax += $row['tax'];
    $totalDiscount += $row['discount'];
    $finalAmount += $row['final_amount'];
}

echo json_encode([
    "sales" => $sales,
    "total_sales" => $totalSales,
    "total_tax" => $totalTax,
    "total_discount" => $totalDiscount,
    "final_amount" => $finalAmount
]);
?>
