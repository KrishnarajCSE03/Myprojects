<?php 
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

include 'config/db.php';
include 'sidebar.php';

// ✅ Fetch all invoices (latest order)
$query = "
    SELECT invoices.*, customers.name AS customer_name, customers.phone, COALESCE(customers.gstn, 'Not Available') AS gstn 
    FROM invoices
    JOIN customers ON invoices.customer_id = customers.customer_id
    WHERE 1
";

// ✅ Apply Filters
if (isset($_POST['filter_submit'])) {
    $conditions = [];
    if (!empty($_POST['customer_name'])) $conditions[] = "customers.name LIKE '%{$_POST['customer_name']}%'";
    if (!empty($_POST['phone'])) $conditions[] = "customers.phone LIKE '%{$_POST['phone']}%'";
    if (!empty($_POST['gstn'])) $conditions[] = "customers.gstn = '{$_POST['gstn']}'";
    if (!empty($_POST['from_date']) && !empty($_POST['to_date'])) {
        $conditions[] = "DATE(invoices.created_at) BETWEEN '{$_POST['from_date']}' AND '{$_POST['to_date']}'";
    }
    if (!empty($conditions)) $query .= " AND " . implode(" AND ", $conditions);
}

$query .= " ORDER BY invoices.created_at DESC";
$invoices = $conn->query($query);

// ✅ Delete Invoice
if (isset($_GET['delete_id'])) {
    $invoice_id = intval($_GET['delete_id']);

    // Start transaction
    $conn->begin_transaction();

    try {
        // Delete related records first
        $conn->query("DELETE FROM invoice_items WHERE invoice_id = $invoice_id");
        $conn->query("DELETE FROM payments WHERE invoice_id = $invoice_id");
        $conn->query("DELETE FROM transpositions WHERE invoice_id = $invoice_id");

        // Finally, delete from invoices
        $conn->query("DELETE FROM invoices WHERE invoice_id = $invoice_id");

        // Commit transaction
        $conn->commit();

        header("Location: invoices.php");
        exit();
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        echo "<script>alert('Failed to delete invoice: " . $e->getMessage() . "');</script>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoices - Billing System</title>
    <link rel="stylesheet" href="assets/invoices.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

<div class="main-content">
    <div class="header">
        <h2>📄 Invoices</h2>
        <a href="add_invoice.php" class="btn btn-success">➕ Add Invoice</a>
    </div>

    <!-- 🛠️ Filter Options -->
    <div class="content-box">
        <h4>🔍 Filter Invoices</h4>
        <form method="POST">
            <div class="filter-group">
                <input type="text" name="customer_name" placeholder="Customer Name">
                <input type="text" name="phone" placeholder="Phone Number">
                <input type="text" name="gstn" placeholder="GSTN Number">
                <label>From: <input type="date" name="from_date"></label>
                <label>To: <input type="date" name="to_date"></label>
                <button type="submit" name="filter_submit" class="btn btn-primary">Filter</button>
            </div>
        </form>
    </div>

    <!-- 📋 Invoice Table -->
    <div class="content-box">
        <h4>📜 All Invoices</h4>
        <table>
            <thead>
                <tr>
                    <th>Invoice #</th>
                    <th>Customer</th>
                    <th>Phone</th>
                    <th>GSTN</th>
                    <th>Total Amount</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $invoices->fetch_assoc()) { ?>
                <tr>
                    <td>#<?php echo $row['invoice_id']; ?></td>
                    <td><?php echo $row['customer_name']; ?></td>
                    <td><?php echo $row['phone']; ?></td>
                    <td><?php echo $row['gstn']; ?></td>
                    <td>₹<?php echo number_format($row['final_amount'], 2); ?></td>
                    <td style="color: <?= ($row['status'] === 'paid') ? 'green' : 'red'; ?>">
                        <?= ucfirst($row['status']); ?>
                    </td>
                    <td>
                        <a href="javascript:void(0);" class="btn btn-info" onclick="openInvoiceModal(<?= $row['invoice_id']; ?>)">🔍 View</a>
                        <a href="invoices.php?delete_id=<?= $row['invoice_id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this invoice?');">❌ Delete</a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ✅ Invoice Modal -->
<div class="modal fade" id="viewInvoiceModal" tabindex="-1" aria-labelledby="invoiceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg rounded-4">
            <div class="modal-header bg-primary text-white rounded-top-4">
                <h5 class="modal-title fw-bold" id="invoiceTitle">Invoice Details</h5>
                <button type="button" class="btn-close bg-light" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body p-4">
                <!-- ✅ Customer Details -->
                <h6 class="text-primary fw-bold">👤 Customer Details</h6>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p><strong>Name:</strong> <span id="customerName"></span></p>
                        <p><strong>Phone:</strong> <span id="customerPhone"></span></p>
                        <p><strong>GSTIN:</strong> <span id="customerGSTIN"></span></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Address:</strong> <span id="customerAddress"></span></p>
                        <p><strong>Pincode:</strong> <span id="customerPincode"></span></p>
                        <p><strong>State:</strong> <span id="customerState"></span></p>
                    </div>
                </div>

<h6 class="text-primary fw-bold">📦 Product Details</h6>
<div class="table-responsive">
    <table class="table table-striped align-middle shadow-sm rounded-3">
        <thead class="bg-light">
            <tr>
                <th>Product Name</th>
                <th>Size</th>
                <th>Quantity</th>
                <th>Unit Price (₹)</th>
                <th>Subtotal (₹)</th>
                
            </tr>
        </thead>
        <tbody id="productTableBody">
            <!-- Dynamic Data Here -->
        </tbody>
    </table>
</div>

<!-- ✅ Payment and Totals -->
<h6 class="text-primary fw-bold">💰 Payment Details</h6>
<p><strong>Total Amount:</strong> ₹<span id="totalAmount"></span></p>
<p><strong>Tax:</strong> ₹<span id="taxAmount"></span></p>
<p><strong>Discount:</strong> ₹<span id="discountAmount"></span></p>
<p><strong>Final Amount:</strong> ₹<span id="finalAmount"></span></p>

                <div class="mb-3">
                    <label class="fw-bold">Payment Status:</label>
                    <select id="paymentStatus" class="form-select shadow-sm">
                        <option value="pending">Pending</option>
                        <option value="paid">Paid</option>
                    </select>
                </div>
            </div>

            <div class="modal-footer bg-light rounded-bottom-4">
                <button class="btn btn-secondary shadow-sm" data-bs-dismiss="modal">❌ Close</button>
                <button class="btn btn-success shadow-sm" onclick="updatePaymentStatus()">✅ Update Status</button>
                <button class="btn btn-primary shadow-sm" onclick="printInvoice()">🖨️ Print Invoice</button>
            </div>
        </div>
    </div>
</div>

<script>
let currentInvoiceId = null;



// ✅ Open and Populate Invoice Modal
function openInvoiceModal(invoiceId) {
    currentInvoiceId = invoiceId;
    
    fetch(`fetch_invoice.php?id=${invoiceId}`)
        .then(res => res.json())
        .then(data => {
            if (data.success) {

                document.getElementById('customerName').innerText = data.invoice.customer_name;
                document.getElementById('customerPhone').innerText = data.invoice.phone;
                document.getElementById('customerGSTIN').innerText = data.invoice.customer_gstn;
                document.getElementById('customerAddress').innerText = data.invoice.customer_address;
                document.getElementById('customerPincode').innerText = data.invoice.pincode;
                document.getElementById('customerState').innerText = data.invoice.state;
                document.getElementById('invoiceTitle').innerText = `Invoice #${data.invoice.invoice_id}`;
                document.getElementById('totalAmount').innerText = data.invoice.total_amount;
                document.getElementById('taxAmount').innerText = data.invoice.tax;
                document.getElementById('discountAmount').innerText = data.invoice.discount;
                document.getElementById('finalAmount').innerText = data.invoice.final_amount;

                let productRows = data.products.map(product => {
                    return `
                        <tr data-product-id="${product.item_id}">
                            <td>${product.name}</td>
                            <td>${product.size}</td>
                            <td><input type="number" class="form-control quantity" value="${product.quantity}" min="1" onchange="recalculate(this)"></td>
                            <td><input type="number" class="form-control unit-price" value="${product.unit_price}" min="0" step="0.01" onchange="recalculate(this)"></td>
                            <td class="subtotal">₹${(product.quantity * product.unit_price).toFixed(2)}</td>
                            
                        </tr>
                    `;
                }).join('');
                document.getElementById('productTableBody').innerHTML = productRows;

                new bootstrap.Modal(document.getElementById('viewInvoiceModal')).show();
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to fetch invoice details!');
        });
}

// ✅ Recalculate Subtotals and Totals in Real-Time
function recalculate(element) {
    const row = element.closest('tr');
    const quantity = parseFloat(row.querySelector('.quantity').value) || 0;
    const unitPrice = parseFloat(row.querySelector('.unit-price').value) || 0;
    const subtotal = quantity * unitPrice;

    row.querySelector('.subtotal').innerText = `₹${subtotal.toFixed(2)}`;

    // Recalculate Overall Totals
    let totalAmount = 0;
    document.querySelectorAll('.subtotal').forEach(cell => {
        totalAmount += parseFloat(cell.innerText.replace('₹', '')) || 0;
    });

    const tax = 0;
    const discount = 0;
    const finalAmount = totalAmount + tax - discount;

    document.getElementById('totalAmount').innerText = totalAmount.toFixed(2);
    document.getElementById('taxAmount').innerText = tax.toFixed(2);
    document.getElementById('discountAmount').innerText = discount.toFixed(2);
    document.getElementById('finalAmount').innerText = finalAmount.toFixed(2);
}

// ✅ Update Product Details in Database
function updateProduct(itemId) {
    const row = document.querySelector(`tr[data-product-id="${itemId}"]`);
    const quantity = row.querySelector('.quantity').value;
    const unitPrice = row.querySelector('.unit-price').value;

    fetch('update_invoice.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `invoice_id=${currentInvoiceId}&item_id=${itemId}&quantity=${quantity}&unit_price=${unitPrice}`
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to update product details!');
    });
}

// ✅ Update Payment Status
function updatePaymentStatus() {
    const status = document.getElementById('paymentStatus').value;
    fetch('update_status.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `invoice_id=${currentInvoiceId}&status=${status}`
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to update payment status!');
    });
}

function printInvoice() {
    window.open(`printinvoice.php?invoice_id=${currentInvoiceId}`, '_blank');
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
