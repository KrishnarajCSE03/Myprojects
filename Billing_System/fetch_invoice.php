<?php
session_start();
include 'config/db.php';

// Validate and fetch invoice ID
$invoiceId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$invoiceId) {
    echo json_encode(['success' => false, 'message' => 'Invalid Invoice ID!']);
    exit();
}

// ✅ Fetch Invoice Details with Customer, Biller Info, and Amount in Words
$invoiceQuery = $conn->prepare("
    SELECT i.*, 
           c.name AS customer_name, c.email AS customer_email, c.phone AS customer_phone, c.address AS customer_address, 
           c.gstn AS customer_gstn, c.pincode, c.district, c.state, c.outstanding_balance,
           u.name AS biller_name,
           i.amount_in_words  -- Include amount in words
    FROM invoices i
    JOIN customers c ON i.customer_id = c.customer_id
    JOIN users u ON i.biller_id = u.user_id
    WHERE i.invoice_id = ?");
$invoiceQuery->bind_param("i", $invoiceId);
$invoiceQuery->execute();
$invoiceResult = $invoiceQuery->get_result();
$invoice = $invoiceResult->fetch_assoc();

if (!$invoice) {
    echo json_encode(['success' => false, 'message' => 'Invoice not found!']);
    exit();
}

// ✅ Fetch Invoice Products (with size)
$productQuery = $conn->prepare("
    SELECT p.name, ii.size, ii.quantity, ii.unit_price, ii.total_price
    FROM invoice_items ii
    JOIN products p ON ii.product_id = p.product_id 
    WHERE ii.invoice_id = ?");
$productQuery->bind_param("i", $invoiceId);
$productQuery->execute();
$productResult = $productQuery->get_result();

$products = [];
while ($product = $productResult->fetch_assoc()) {
    $products[] = [
        'name' => $product['name'],
        'size' => $product['size'],  
        'quantity' => (float)$product['quantity'],
        'unit_price' => (float)$product['unit_price'],
        'total_price' => (float)$product['total_price']
    ];
}

// ✅ Fetch Payment Details
$paymentQuery = $conn->prepare("
    SELECT amount_paid, payment_method, payment_date 
    FROM payments 
    WHERE invoice_id = ?");
$paymentQuery->bind_param("i", $invoiceId);
$paymentQuery->execute();
$paymentResult = $paymentQuery->get_result();

$payments = [];
while ($payment = $paymentResult->fetch_assoc()) {
    $payments[] = [
        'amount_paid' => $payment['amount_paid'],
        'payment_method' => $payment['payment_method'],
        'payment_date' => $payment['payment_date']
    ];
}

// ✅ Return Data as JSON
header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'invoice' => $invoice,
    'products' => $products,
    'payments' => $payments
]);
exit;
?>
