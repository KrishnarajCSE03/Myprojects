<?php
header('Content-Type: application/json');
session_start();
include 'config/db.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["error" => "Invalid request method."]);
    exit();
}

if (!isset($_POST['customer_id1']) || empty($_POST['customer_id1'])) {
    echo json_encode(["error" => "Customer ID is missing."]);
    exit();
}

$customer_id = $_POST['customer_id1'];
$tax = $_POST['tax'] ?? 0;
$discount = $_POST['discount'] ?? 0;
$status = $_POST['payment_status'] ?? "pending";
$payment_method = $_POST['payment_method'] ?? "Cash";
$transactionNo = isset($_POST['transaction_no']) ? trim($_POST['transaction_no']) : null;
$amount_paid = $_POST['amount_paid'] ?? 0;
$amount_in_words = $_POST['amount_in_words'] ?? '';

$total_amount = 0;
$total_quantity = 0;
$products = $_POST['products'] ?? [];

if (empty($products)) {
    echo json_encode(["error" => "No products added."]);
    exit();
}

$conn->begin_transaction();

try {
    // Validate customer existence
    $stmt = $conn->prepare("SELECT customer_id FROM customers WHERE customer_id = ?");
    $stmt->bind_param("i", $customer_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(["error" => "Customer not found."]);
        $conn->rollback();
        exit();
    }

    // Calculate total amount and quantity
    foreach ($products as $product) {
        $quantity = $product['quantity'] ?? 0;
        $price = $product['price'] ?? 0;
        $total_quantity += $quantity;
        $total_amount += $quantity * $price;
    }

    $discount_amount = ($total_amount * $discount) / 100;
    $final_amount = ($total_amount + $tax) - $discount_amount;

    if ($status === 'paid' && $amount_paid <= 0) {
        $amount_paid = $final_amount;
    }

    // Find the smallest unused invoice_id
    $idQuery = $conn->query("
        SELECT MIN(t1.invoice_id + 1) AS next_id 
        FROM invoices t1 
        LEFT JOIN invoices t2 ON t1.invoice_id + 1 = t2.invoice_id 
        WHERE t2.invoice_id IS NULL
    ");
    $idResult = $idQuery->fetch_assoc();
    $invoice_id = $idResult['next_id'] ?? 1;

    $biller_id = 1;
    $due_date = date('Y-m-d', strtotime('+7 days'));

    // Insert into invoices
    $stmt = $conn->prepare("
        INSERT INTO invoices (invoice_id, customer_id, biller_id, total_amount, tax, discount, final_amount, amount_in_words, status, due_date, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ");
    $stmt->bind_param("iiidddssss", $invoice_id, $customer_id, $biller_id, $total_amount, $tax, $discount_amount, $final_amount, $amount_in_words, $status, $due_date);
    
    if (!$stmt->execute()) {
        throw new Exception("Error inserting invoice: " . $stmt->error);
    }

    // Insert invoice items
    $stmt = $conn->prepare("
        INSERT INTO invoice_items (invoice_id, product_id, size, quantity, unit_price, total_price) 
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    foreach ($products as $product) {
        $product_id = $product['id'] ?? 0;
        $size = $product['size'] ?? '';
        $quantity = $product['quantity'] ?? 0;
        $price = $product['price'] ?? 0;
        $total = $quantity * $price;

        $stmt->bind_param("iisdid", $invoice_id, $product_id, $size, $quantity, $price, $total);
        if (!$stmt->execute()) {
            throw new Exception("Error inserting invoice item: " . $stmt->error);
        }
    }

    // Validate payment details
    if (in_array($payment_method, ['UPI', 'Bank Transfer']) && empty($transactionNo)) {
        throw new Exception("Transaction number is required for UPI or Bank Transfer payments.");
    }

    // Insert payment details
    $payment_date = date('Y-m-d');
    $paymentQuery = $conn->prepare("
        INSERT INTO payments (invoice_id, amount_paid, payment_method, payment_date, transaction_no)
        VALUES (?, ?, ?, ?, ?)
    ");
    $paymentQuery->bind_param("idsss", $invoice_id, $amount_paid, $payment_method, $payment_date, $transactionNo);

    if (!$paymentQuery->execute()) {
        throw new Exception("Error inserting payment record: " . $paymentQuery->error);
    }

    // Insert or Update accounts table
    $checkAccount = $conn->prepare("SELECT * FROM accounts WHERE customer_id = ?");
    $checkAccount->bind_param("i", $customer_id);
    $checkAccount->execute();
    $accountResult = $checkAccount->get_result();

    $balance_due = $final_amount - $amount_paid;
    $status = ($balance_due > 0) ? "pending" : "paid";

    if ($accountResult->num_rows > 0) {
        // Update existing account
        $updateAccount = $conn->prepare("
            UPDATE accounts 
            SET total_due = total_due + ?, 
                total_paid = total_paid + ?, 
                balance_due = balance_due + ?, 
                last_payment_date = ?, 
                status = ?
            WHERE customer_id = ?
        ");
        $updateAccount->bind_param("dddssi", $final_amount, $amount_paid, $balance_due, $payment_date, $status, $customer_id);
        $updateAccount->execute();
    } else {
        // Insert new account
        $insertAccount = $conn->prepare("
            INSERT INTO accounts (customer_id, biller_id, total_due, total_paid, balance_due, last_payment_date, status, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        $insertAccount->bind_param("iidddss", $customer_id, $biller_id, $final_amount, $amount_paid, $balance_due, $payment_date, $status);
        $insertAccount->execute();
    }

    $conn->commit();

    echo json_encode([
        "success" => true,
        "invoice_id" => $invoice_id,
        "message" => "Invoice and account updated successfully with ID: $invoice_id"
    ]);
    exit;

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(["error" => $e->getMessage()]);
    exit();
}
?>
