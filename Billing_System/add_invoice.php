<?php 
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

include 'config/db.php';
include 'sidebar.php';


// Fetch products
$products = $conn->query("SELECT * FROM products");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Invoice - Billing System</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    
    <!-- Font Awesome (Icons) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>

    

    <!-- jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>



    <!-- Internal Styles -->
    <style>
        body {
            background: linear-gradient(135deg, #f0f4ff, #d9e4f5);
            font-family: 'Poppins', sans-serif;
        }
        .invoice-container {
            max-width: 900px;
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
            margin-left: 330px;
            margin-top:20px;
            margin-bottom:20px;
            


        }
        .btn-remove {
            background: #28a745;
            color: white;
            border-radius: 50%;
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: 0.3s;
            border: none;
            
        }
        /* Center and Bold Only for Section Titles */
.section-title {
    font-weight: bold;
    text-align: center;
    font-size: 16px;
    display: block;
    margin-top: 15px;
    color:rgb(44, 44, 44); /* Optional: Blue color for styling */
    margin: 15px;
}

        .btn-remove:hover {
            background: #218838;
            transform: scale(1.1);
        }
    </style>

</head>
<body>

<div class="invoice-container">
    <h2 class="text-center"><i class="fas fa-file-invoice"></i> Create New Invoice</h2>

<!-- ✅ Alert Messages -->
<?php if (isset($_SESSION['message'])): ?>
    <div class="alert <?php echo isset($_SESSION['message']['success']) ? 'alert-success' : 'alert-danger'; ?> alert-dismissible fade show" role="alert">
        <?php echo isset($_SESSION['message']['success']) ? $_SESSION['message']['success'] : $_SESSION['message']['error']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['message']); ?>
<?php endif; ?>

<!-- 🔍 Search Customer -->
<label class="section-title">Search Customer</label>
<form id="searchCustomerForm" class="mb-3">
    <div class="input-group">
        <input type="text" id="customerMobile" name="mobile" class="form-control" placeholder="Enter Customer Mobile" required>
        <button type="button" class="btn btn-primary" onclick="fetchCustomer()"><i class="fas fa-search"></i> Search</button>
    </div>
</form>

<!-- ⚠️ Alert for Customer Not Found -->
<div id="customerNotFound" class="alert alert-warning d-none">
    <strong>Customer not found!</strong> Would you like to add a new customer?
    <button class="btn btn-success btn-sm" onclick="redirectToAddCustomer()">➕ Add Customer</button>
</div>

<!-- 📜 Customer Details -->
<div id="customerDetails" style="display: none;">
    <label>Name</label>
    <input type="text" id="customerName" class="form-control" readonly>

    <label>GSTIN</label>
    <input type="text" id="customerGSTIN" class="form-control" readonly>

    <label>Address</label>
    <textarea id="customerAddress" class="form-control" readonly></textarea>

    <label>Pincode</label>
    <input type="text" id="customerPincode" class="form-control" readonly>

    <label>District</label>
    <input type="text" id="customerDistrict" class="form-control" readonly>

    <label>State</label>
    <input type="text" id="customerState" class="form-control" readonly>

    <input type="hidden" name="customer_id" id="customerID">
</div>


    <!-- 📄 Invoice Form -->
    <form id="invoiceForm" action="process_invoice.php" method="POST">
        <input type="hidden" name="customer_id1" id="customerID1">

        <br/>

        <label class="section-title">Product Section</label>

        <!-- 📦 Product Section -->
        <div id="product-section">
    <div class="product-group d-flex gap-3 mb-2">
        <select name="products[0][id]" class="form-select product-select" required>
            <option value="" disabled selected>Select Product</option>
            <?php while ($product = $products->fetch_assoc()): ?>
                <option value="<?= $product['product_id']; ?>" data-size="<?= $product['product_size']; ?>">
                    <?= $product['name']; ?>

                </option>

            <?php endwhile; ?>
        </select>
        <input type="text" name="products[0][size]" placeholder="Size" class="form-control" required>
        <input type="number" name="products[0][quantity]" placeholder="Qty" class="form-control quantity" required>
        <input type="number" name="products[0][price]" placeholder="Price" class="form-control price" required>
        <input type="text" class="form-control subtotal" placeholder="Subtotal" readonly>
        <button type="button" class="btn btn-remove" onclick="removeProduct(this)"><i class="fas fa-trash"></i></button>
    </div>
</div>



        <!-- ➕ Add Product Button -->
        <button type="button" class="btn btn-success mb-3" onclick="addProduct()"><i class="fas fa-plus-circle"></i> Add Product</button>

        <label class="section-title">Payment Details</label>
        <div class="mb-3">
            <label>Payment Status</label>
            <select name="payment_status" class="form-select">
            <option value="paid" selected>Paid</option>
            <option value="pending" >Pending</option>
                
            </select>
        </div>
        <div class="mb-3">
    <label>Payment Method</label>
    <select name="payment_method" id="paymentMethod" class="form-select">
        <option value="UPI">UPI (GPAY, PHONEPE, PAYTM, ETC..)</option>
        <option value="Cash">Cash</option>
        <option value="Bank Transfer">Bank Transfer</option>
    </select>
</div>

<!-- 🔢 Transaction Number -->
<div class="mb-3 d-none" id="transactionNumberField">
    <label>Transaction Number</label>
    <input type="text" name="transaction_no" id="transactionNo" class="form-control">
</div>


        <!-- 💰 Totals Section -->
<!-- 💰 Totals Section -->
<div class="totals">
    <label>Total Qty</label>
    <input type="number" id="totalQuantity" class="form-control" readonly>

    <label>Total Amount</label>
    <input type="number" name="total_amount" id="totalAmount" class="form-control" readonly>

    <label>Tax</label>
    <input type="number" name="tax" id="tax" class="form-control" min="0" value="0" required oninput="calculateTotals()">

    <label>Discount (%)</label>
    <input type="number" name="discount" id="discount" class="form-control" min="0" max="100" value="0" required oninput="calculateTotals()">

    <label>Final Amount</label>
    <input type="number" name="final_amount" id="finalAmount" class="form-control" readonly>

    <label>Amount in Words</label>
    <input type="text" id="amountInWords" class="form-control" readonly>
    <input type="hidden" id="amount_in_words" name="amount_in_words">

</div>



<!-- ✅ Submit Button -->
<button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#confirmInvoiceModal">
  <i class="fas fa-check-circle"></i> Create Invoice
</button>


    </form>
</div>


<!-- Confirm Invoice Modal -->
<div class="modal fade" id="confirmInvoiceModal" tabindex="-1" aria-labelledby="confirmInvoiceModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmInvoiceModalLabel">Confirm Invoice Creation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to create this invoice?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="finalSubmitBtn" class="btn btn-primary">Confirm</button>
            </div>
        </div>
    </div>
</div>
<!-- Processing Modal -->
<div class="modal fade" id="processingModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content text-center p-3">
            <h4>Processing Invoice...</h4>
            <p>Please wait...</p>
        </div>
    </div>
</div>



<!-- Transposition Modal -->
<div class="modal fade" id="transpositionModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Transport Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
            <form id="transpositionForm">
    <!-- Invoice ID -->
    <input type="hidden" name="invoice_id" id="invoiceId">

    <!-- Customer ID -->
    <input type="hidden" name="customer_id" id="customerId">

    <!-- From Location -->
    <label>From Location</label>
    <input type="text" name="from_location" class="form-control" placeholder="Enter starting location" required>

    <!-- Destination -->
    <label>Destination</label>
    <input type="text" name="destination" class="form-control" placeholder="Enter destination" required>

    <!-- Transport Through -->
    <label>Transport Through</label>
    <select name="transport_through" class="form-select" required>
        <option value="" disabled selected>Select Transport Type</option>
        <option value="Courier">Courier</option>
        <option value="Hand Delivery">Hand Delivery</option>
        <option value="Lorry">Lorry</option>
    </select>

    <!-- Transport Company -->
    <label>Transport Company (Optional)</label>
    <input type="text" name="transport_company" class="form-control" placeholder="Enter transport company">

    <!-- Tracking Number -->
    <label>Tracking Number (Optional)</label>
    <input type="text" name="tracking_number" class="form-control" placeholder="Enter tracking number">

    <!-- Status -->
    <label>Status</label>
    <select name="status" class="form-select" required>
        <option value="Pending">Pending</option>
        <option value="In Transit">In Transit</option>
        <option value="Delivered">Delivered</option>
    </select>

    

</form>

            </div>
            <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
    <button type="button" id="submitTranspositionButton" class="btn btn-primary">Save Transport Details</button>
</div>
        </div>
    </div>
</div>

<!-- Invoice Success Modal -->
<div class="modal fade" id="invoiceModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Invoice Created</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">What would you like to do next?</div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button class="btn btn-primary" onclick="redirectToPrint()">Print Invoice</button>
            </div>
        </div>
    </div>
</div>





<script src="invoice.js"></script>


<!-- Bootstrap Bundle JS (includes Popper.js for Bootstrap components) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
