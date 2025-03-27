<?php
include 'db_connection.php'; // Replace with your actual DB connection file

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $invoiceId = $_POST['invoice_id'] ?? null;
    $itemId = $_POST['item_id'] ?? null;
    $quantity = $_POST['quantity'] ?? null;
    $unitPrice = $_POST['unit_price'] ?? null;

    // Validate inputs
    if (!$invoiceId || !$itemId || !$quantity || !$unitPrice) {
        echo json_encode(['success' => false, 'message' => 'Invalid input data!']);
        exit;
    }

    // Ensure correct data types
    $quantity = (float)$quantity;
    $unitPrice = (float)$unitPrice;
    $totalPrice = $quantity * $unitPrice;

    // ✅ Start Transaction
    $conn->begin_transaction();

    try {
        // 1️⃣ Check if item exists
        $checkItem = $conn->prepare("SELECT * FROM invoice_items WHERE item_id = ?");
        $checkItem->bind_param("i", $itemId);
        $checkItem->execute();
        $itemResult = $checkItem->get_result();

        if ($itemResult->num_rows === 0) {
            throw new Exception('Item ID not found!');
        }

        // 2️⃣ Update Product Details
        $updateProduct = $conn->prepare("UPDATE invoice_items SET quantity = ?, unit_price = ?, total_price = ? WHERE item_id = ?");
        if (!$updateProduct) {
            throw new Exception('Prepare failed: ' . $conn->error);
        }

        $updateProduct->bind_param("dddi", $quantity, $unitPrice, $totalPrice, $itemId);

        if (!$updateProduct->execute()) {
            throw new Exception('Execute failed: ' . $updateProduct->error);
        }

        // No rows updated but data might be identical
        if ($updateProduct->affected_rows === 0) {
            throw new Exception('No changes detected or item_id not matching!');
        }

        // 3️⃣ Recalculate Overall Totals for the Invoice
        $totalResult = $conn->prepare("SELECT SUM(total_price) AS total FROM invoice_items WHERE invoice_id = ?");
        $totalResult->bind_param("i", $invoiceId);
        $totalResult->execute();
        $totalResult = $totalResult->get_result();
        $row = $totalResult->fetch_assoc();

        $totalAmount = $row['total'] ?? 0;
        $tax = $totalAmount * 0.18;
        $discount = $totalAmount * 0.05;
        $finalAmount = $totalAmount + $tax - $discount;

        // 4️⃣ Update Invoice Totals
        $updateInvoice = $conn->prepare("UPDATE invoice SET total_amount = ?, tax = ?, discount = ?, final_amount = ? WHERE invoice_id = ?");
        if (!$updateInvoice) {
            throw new Exception('Prepare failed: ' . $conn->error);
        }

        $updateInvoice->bind_param("ddddi", $totalAmount, $tax, $discount, $finalAmount, $invoiceId);

        if (!$updateInvoice->execute()) {
            throw new Exception('Execute failed: ' . $updateInvoice->error);
        }

        // Commit the Transaction
        $conn->commit();
        echo json_encode(['success' => true, 'message' => 'Product and invoice updated successfully!']);

    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }

} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method!']);
}

$conn->close();
?>
