<?php
include 'config/db.php';

$format = $_GET['format'] ?? '';
$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';

if (!$format || !$from || !$to) {
    die("Invalid input. Please select the format and date range.");
}

// Comprehensive query to gather all data
$query = $conn->prepare("
    SELECT 
        invoices.invoice_id,
        invoices.total_amount,
        invoices.tax,
        invoices.discount,
        invoices.final_amount,
        invoices.amount_in_words,
        invoices.status AS invoice_status,
        invoices.due_date,
        invoices.created_at AS invoice_date,

        customers.name AS customer_name,
        customers.email AS customer_email,
        customers.phone AS customer_phone,
        customers.gstn AS customer_gstn,
        customers.address AS customer_address,
        customers.pincode AS customer_pincode,
        customers.district AS customer_district,
        customers.state AS customer_state,

        products.name AS product_name,
        invoice_items.size AS product_size,
        invoice_items.quantity,
        invoice_items.unit_price,
        invoice_items.total_price,

        payments.amount_paid,
        payments.payment_method,
        payments.payment_date,

        transpositions.from_location,
        transpositions.destination,
        transpositions.transport_through,
        transpositions.transport_company,
        transpositions.tracking_number,
        transpositions.status AS transport_status

    FROM invoices
    LEFT JOIN customers ON invoices.customer_id = customers.customer_id
    LEFT JOIN invoice_items ON invoices.invoice_id = invoice_items.invoice_id
    LEFT JOIN products ON invoice_items.product_id = products.product_id
    LEFT JOIN payments ON invoices.invoice_id = payments.invoice_id
    LEFT JOIN transpositions ON invoices.invoice_id = transpositions.invoice_id
    WHERE invoices.created_at BETWEEN ? AND ?
");

$query->bind_param("ss", $from, $to);
$query->execute();
$result = $query->get_result();

$filename = "sales_report_{$from}_to_{$to}";

// Prepare headers for CSV and Excel
$headers = [
    'Invoice ID', 'Invoice Date', 'Total Amount', 'Tax', 'Discount', 'Final Amount', 'Amount in Words', 'Invoice Status', 'Due Date',
    'Customer Name', 'Customer Email', 'Customer Phone', 'Customer GSTN', 'Customer Address', 'Pincode', 'District', 'State',
    'Product Name', 'Product Size', 'Quantity', 'Unit Price', 'Total Price',
    'Amount Paid', 'Payment Method', 'Payment Date',
    'From Location', 'Destination', 'Transport Through', 'Transport Company', 'Tracking Number', 'Transport Status'
];

if ($format === 'csv') {
    header('Content-Type: text/csv');
    header("Content-Disposition: attachment; filename=\"{$filename}.csv\"");

    $output = fopen('php://output', 'w');
    fputcsv($output, $headers);

    while ($row = $result->fetch_assoc()) {
        fputcsv($output, [
            $row['invoice_id'], $row['invoice_date'], $row['total_amount'], $row['tax'], $row['discount'], $row['final_amount'],
            $row['amount_in_words'], $row['invoice_status'], $row['due_date'],
            $row['customer_name'], $row['customer_email'], $row['customer_phone'], $row['customer_gstn'], $row['customer_address'],
            $row['customer_pincode'], $row['customer_district'], $row['customer_state'],
            $row['product_name'], $row['product_size'], $row['quantity'], $row['unit_price'], $row['total_price'],
            $row['amount_paid'], $row['payment_method'], $row['payment_date'],
            $row['from_location'], $row['destination'], $row['transport_through'], $row['transport_company'], $row['tracking_number'], $row['transport_status']
        ]);
    }

    fclose($output);

} elseif ($format === 'excel') {
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=\"{$filename}.xls\"");

    echo implode("\t", $headers) . "\n";

    while ($row = $result->fetch_assoc()) {
        echo implode("\t", [
            $row['invoice_id'], $row['invoice_date'], $row['total_amount'], $row['tax'], $row['discount'], $row['final_amount'],
            $row['amount_in_words'], $row['invoice_status'], $row['due_date'],
            $row['customer_name'], $row['customer_email'], $row['customer_phone'], $row['customer_gstn'], $row['customer_address'],
            $row['customer_pincode'], $row['customer_district'], $row['customer_state'],
            $row['product_name'], $row['product_size'], $row['quantity'], $row['unit_price'], $row['total_price'],
            $row['amount_paid'], $row['payment_method'], $row['payment_date'],
            $row['from_location'], $row['destination'], $row['transport_through'], $row['transport_company'], $row['tracking_number'], $row['transport_status']
        ]) . "\n";
    }
    
} else {
    echo "Unsupported format selected.";
}
?>
