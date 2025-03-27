<?php 
include 'config/db.php';

// Validate and fetch invoice ID
$invoice_id = isset($_GET['invoice_id']) ? intval($_GET['invoice_id']) : 0;
if (!$invoice_id) {
    die("Invalid Invoice ID.");
}

// Fetch invoice and customer details
$invoice_query = "
    SELECT invoices.*, customers.name AS customer_name, customers.phone, customers.address, customers.pincode,
           customers.district, customers.state, customers.gstn, invoices.amount_in_words
    FROM invoices
    JOIN customers ON invoices.customer_id = customers.customer_id
    WHERE invoices.invoice_id = ?
";
$stmt = $conn->prepare($invoice_query);
$stmt->bind_param("i", $invoice_id);
$stmt->execute();
$invoice_result = $stmt->get_result();
$invoice = $invoice_result->fetch_assoc();
$stmt->close();

// Fetch product details
$product_query = "
    SELECT products.name, invoice_items.size, invoice_items.quantity, invoice_items.unit_price, invoice_items.total_price
    FROM invoice_items
    JOIN products ON invoice_items.product_id = products.product_id
    WHERE invoice_items.invoice_id = ?
";
$stmt = $conn->prepare($product_query);
$stmt->bind_param("i", $invoice_id);
$stmt->execute();
$product_result = $stmt->get_result();
$stmt->close();

// Fetch transportation details
$trans_query = "SELECT * FROM transpositions WHERE invoice_id = ?";
$stmt = $conn->prepare($trans_query);
$stmt->bind_param("i", $invoice_id);
$stmt->execute();
$trans_result = $stmt->get_result();
$trans = $trans_result->fetch_assoc();
$stmt->close();

if (!$invoice) {
    die("Invoice not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #<?php echo htmlspecialchars($invoice['invoice_id']); ?></title>
    <link rel="stylesheet" href="invoice-style.css">
</head>
<body>

<div class="invoice-box">
    <h2>BILL OF SUPPLY</h2>

    <div class="header">
        <div class="logo">
            <img src="assets/logo.png" alt="Company Logo">
        </div>

        <div class="company-details">
            <h3>M/s. RAKSHINI TRADERS</h3>
            <p>3/484, Eswari Nagar, Thirunagar, Madurai-625 006 (T.N.)</p>
        </div>
    </div>

    <div class="extra-info">
        <p>GSTIN: 33JXHPS2191M1ZS</p>
        <p>Ph: 8925553286, 9791697294</p>
    </div>
</div>

<!-- Invoice and Customer Details -->
<div class="details-box">
    <div class="left-section">
        <p><strong>Invoice No:</strong> <?php echo htmlspecialchars($invoice['invoice_id']); ?></p>
        <p><strong>Date:</strong> <?php echo date('d-m-Y', strtotime($invoice['created_at'])); ?></p>
    </div>
    <div class="right-section">
        <p><strong>To:</strong></p>
        <p><strong>Customer Name:</strong> <?php echo htmlspecialchars($invoice['customer_name']); ?></p>
        <p><strong>Customer GSTIN:</strong> <?php echo htmlspecialchars($invoice['gstn']); ?></p>
        <p><strong>Address:</strong> <?php echo htmlspecialchars($invoice['address']); ?></p>
        <p><strong>Pincode:</strong> <?php echo htmlspecialchars($invoice['pincode']); ?> | <strong>District:</strong> <?php echo htmlspecialchars($invoice['district']); ?> | <strong>State:</strong> <?php echo htmlspecialchars($invoice['state']); ?></p>
    </div>
</div>

<!-- Transportation Details -->
<div class="transportation">
    <p>
        <span><strong>From:</strong> <?php echo htmlspecialchars($trans['from_location'] ?? 'N/A'); ?></span>
        <span><strong>Destination:</strong> <?php echo htmlspecialchars($trans['destination'] ?? 'N/A'); ?></span>
        <span><strong>Despatch Through:</strong> <?php echo htmlspecialchars($trans['transport_company'] ?? 'N/A'); ?></span>
    </p>
</div>

<!-- Product Details -->
<table class="table">
    <thead>
        <tr>
            <th>S.No</th>
            <th>Particulars</th>
            <th>Size</th>
            <th>Qty</th>
            <th>Rate (₹)</th>
            <th>Amount (₹)</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $total = 0;
        $serialNo = 1; 
        while ($product = $product_result->fetch_assoc()) {
            $total += $product['total_price'];
            echo "<tr>
                <td>{$serialNo}</td>
                <td>".htmlspecialchars($product['name'])."</td>
                <td>".htmlspecialchars($product['size'])."</td>
                <td>".htmlspecialchars($product['quantity'])."</td>
                <td>₹" . number_format($product['unit_price'], 2) . "</td>
                <td>₹" . number_format($product['total_price'], 2) . "</td>
            </tr>";
            $serialNo++;
        } ?>
        <tr class="total-row">
            <td colspan="5" class="text-right"><strong>Total</strong></td>
            <td>₹<?php echo number_format($total, 2); ?></td>
        </tr>
    </tbody>
</table>

<!-- Amount in Words -->
<p class="amount-words"><strong>Amount in Words:</strong> <?php echo htmlspecialchars($invoice['amount_in_words'] ?? 'Not Available'); ?></p>

<!-- Bank Details and Signature -->
<div class="bank-signature-box">
    <div class="bank-details">
        <p><strong>BANK DETAILS</strong></p>
        <p>M/s. RAKSHINI TRADERS</p>
        <p>Bank: Union Bank of India</p>
        <p>Account No.: 444901010038147</p>
        <p>Branch: Vilacheri, Madurai.</p>
        <p>IFSC Code: UBIN0544493</p>
        <p><strong>HSN Code:</strong> 5607</p>
    </div>

    <div class="signature">
        <p>For M/s. Rakshini Traders</p>
        <div class="signature-block">
            <img src="assets/signature.png" alt="Authorized Signature" class="signature-image">
            <p class="proprietor">Proprietor</p>
        </div>
    </div>
</div>

<!-- Footer -->
<div class="footer">
    <p>Government of India Ministry of Finance (Department of Revenue) | Notification No.2/2017 - Integrated Tax (Rate)</p>
    <p>SACRED THREAD, KALAVA - RAKSHA SUTRA w.e.f. 28.06.2017</p>
</div>

<script>
    window.onload = function () {
        window.print();
    };
</script>

</body>
</html>
