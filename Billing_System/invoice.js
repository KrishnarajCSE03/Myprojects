document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("tax").value = 0;      // Default tax
    document.getElementById("discount").value = 0; // Default discount
    calculateTotals(); // Initialize totals
    toggleTransactionField(); // Initial check
    document.getElementById('paymentMethod').addEventListener('change', toggleTransactionField);
    const transpositionModal = new bootstrap.Modal(document.getElementById("transpositionModal"), {
        backdrop: "static",
        keyboard: false
});



});
// Attach a single listener for dynamic elements
document.addEventListener("input", function (event) {
    if (event.target.classList.contains("quantity") || event.target.classList.contains("price")) {
        updateSubtotal(event.target);
    } else if (event.target.id === "tax" || event.target.id === "discount") {
        calculateTotals();
    }
   
});



let customerID = null;
// 🚀 Redirect to Add Customer Page
function redirectToAddCustomer() {
    window.location.href = "add_customer.php?redirect=add_invoice.php";
}

// Prevent Form Submission if Customer ID is Missing
document.getElementById("invoiceForm").addEventListener("submit", function(event) {
    customerID = document.getElementById("customerID").value.trim();
    if (customerID === "") {
        event.preventDefault();
        
        alert("Error: Customer ID is missing. Please search for a customer first.");
    }
    event.preventDefault();

    showInvoiceModal(); 
    setTimeout(() => {
        showInvoiceModal(); // Show modal after invoice is "created"
    }, 500);

});

// ✅ Show the Invoice Modal
function showInvoiceModal() {
    let invoiceModal = new bootstrap.Modal(document.getElementById('invoiceModal'));
    invoiceModal.show();
    
}

let lastInvoiceId = null;
let invoiceModalInstance;

// ✅ Initialize default values and totals on page load
document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("tax").value = 0;
    document.getElementById("discount").value = 0;
    document.getElementById("customerNotFound").classList.add("d-none"); // Hide "Customer Not Found" alert
    calculateTotals(); // Initialize totals
    checkProductCount(); // Ensure correct delete button visibility
});

// ✅ Dynamic event listener for input changes (quantity, price, tax, discount)
document.addEventListener("input", function (event) {
    if (event.target.classList.contains("quantity") || event.target.classList.contains("price")) {
        updateSubtotal(event.target);
    } else if (event.target.id === "tax" || event.target.id === "discount") {
        calculateTotals();
    }
});

document.getElementById("invoiceForm").addEventListener("submit", function (event) {
    event.preventDefault();
    submitInvoiceForm(); // Call function when form is submitted
});

// ✅ Fetch customer details via mobile number
function fetchCustomer() {
    let mobile = document.getElementById("customerMobile").value.trim();
    if (mobile === "") {
        alert("Please enter a valid mobile number!");
        return;
    }

    fetch("fetch_customer.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "mobile=" + encodeURIComponent(mobile)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById("customerDetails").style.display = "block";
            document.getElementById("customerID").value = data.customer_id;
            document.getElementById("customerID1").value = data.customer_id;
            document.getElementById("customerName").value = data.name;
            document.getElementById("customerGSTIN").value = data.gstn;
            document.getElementById("customerAddress").value = data.address;
            document.getElementById("customerPincode").value = data.pincode;
            document.getElementById("customerDistrict").value = data.district;
            document.getElementById("customerState").value = data.state;
            document.getElementById("customerNotFound").classList.add("d-none");
        } else {
            document.getElementById("customerDetails").style.display = "none";
            document.getElementById("customerID").value = "";
            document.getElementById("customerNotFound").classList.remove("d-none");
        }
    })
    .catch(error => console.error("Error Fetching Customer:", error));
}



// ✅ Final Submit Button Logic
document.getElementById("finalSubmitBtn").addEventListener("click", function () {
    // Close the confirmation modal
    $("#confirmInvoiceModal").modal("hide");

    // Trigger the actual form submission
    submitInvoiceForm();
});

// ✅ Main Form Submission Logic
function submitInvoiceForm() {
    let customerID = document.getElementById("customerID1").value.trim();
    if (!customerID) {
        alert("Error: Customer ID is missing. Please search for a customer first.");
        return;
    }

    // Validate transaction number if payment method is not Cash
    const paymentMethod = document.getElementById("paymentMethod").value;
    const transactionNo = document.getElementById("transactionNo").value.trim();

    if (paymentMethod !== "Cash" && !transactionNo) {
        alert("Transaction number is required for non-cash payments.");
        return;
    }

    const form = document.getElementById("invoiceForm");
    const formData = new FormData(form);

    fetch("process_invoice.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.text())
    .then(text => {
        console.log("Server Response:", text);

        try {
            const data = JSON.parse(text);
            return data;
        } catch (error) {
            console.error("JSON Parse Error:", error, "\nRaw Response:", text);
            alert("Invalid JSON response from server.");
            return null;
        }
    })
    .then(data => {
        if (!data) return;

        if (data.success && data.invoice_id) {
            console.log("✅ Generated Invoice ID:", data.invoice_id);
            lastInvoiceId = data.invoice_id;
            showTranspositionForm();
        } else {
            console.error("❌ Invoice ID Missing in Response:", data);
            alert(data.message || "An error occurred while creating the invoice.");
        }
    })
    .catch(error => {
        console.error("Fetch Error:", error);
        alert("Network error or server issue.");
    });
    
}







let invoiceIdt = null;
let customerIdt = null;

// ✅ Show Transport Entry Form
function showTranspositionForm() {
    customerIdt = document.getElementById("customerID1").value.trim();
    invoiceIdt = lastInvoiceId;

    if (!invoiceIdt || !customerIdt) {
        alert("❌ Error: Invoice ID and Customer ID are required before opening the form.");
        return;
    }

    // Assign values to hidden input fields
    document.getElementById("invoiceId").value = invoiceIdt;
    document.getElementById("customerId").value = customerIdt;

    $("#transpositionModal").modal("show");
}

// ✅ Manual Backdrop Removal if Bootstrap Fails
document.getElementById('transpositionModal').addEventListener('hidden.bs.modal', function () {
    document.body.classList.remove('modal-open');
    document.querySelector('.modal-backdrop')?.remove();
});

// ✅ Submit Transport Form & Print Invoice
function submitTransposition(event) {
    event.preventDefault();

    const submitButton = document.getElementById("submitTranspositionButton");
    submitButton.disabled = true; // Disable button to prevent multiple submissions

    const invoiceId = document.getElementById("invoiceId").value;
    const customerId = document.getElementById("customerId").value;

    if (!invoiceId || !customerId) {
        alert("❌ Error: Invoice ID and Customer ID are required.");
        submitButton.disabled = false; // Re-enable on validation failure
        return;
    }

    const form = document.getElementById("transpositionForm");
    const formData = new FormData(form);

    $.ajax({
        url: "save_transport.php",
        method: "POST",
        data: formData,
        processData: false,
        contentType: false,
        dataType: "json",
        success: function (response) {
            if (response.success) {
                alert("✅ Transport details added successfully.");
                $("#transpositionModal").modal("hide");
                $("#invoiceModal").modal("show");
            } else {
                alert("❌ Error: " + (response.message || "Failed to add transport details."));
            }
        },
        error: function (xhr, status, error) {
            console.error("❌ AJAX Error:", status, error);
            console.error("❌ Raw Response:", xhr.responseText);
            alert("❌ An error occurred while adding transport details. Check console for more details.");
        },
        complete: function () {
            submitButton.disabled = false; // Re-enable the button after request is complete
        }
    });
}


// ✅ Attach Event Listener to Submit Button Instead of Form
// ✅ Attach Event Listener to Submit Button
document.getElementById("submitTranspositionButton").removeEventListener("click", submitTransposition);
document.getElementById("submitTranspositionButton").addEventListener("click", submitTransposition);




// ✅ Function to reset both forms
function resetForms() {
    // Reset Invoice Form
    document.getElementById('invoiceForm').reset();
    
    // Reset Product Section to initial state
    $('#product-section').html(`
        
    <div class="input-group">
        <input type="text" id="customerMobile" name="mobile" class="form-control" placeholder="Enter Customer Mobile" required>
        <button type="button" class="btn btn-primary" onclick="fetchCustomer()"><i class="fas fa-search"></i> Search</button>
    </div>

        <div class="product-group d-flex gap-3 mb-2">
            <select name="products[0][id]" class="form-select product-select" required>
                <option value="" disabled selected>Select Product</option>
                <?php 
                $products->data_seek(0); // Reset pointer to fetch products again
                while ($product = $products->fetch_assoc()): ?>
                    <option value="<?= $product['product_id']; ?>" data-size="<?= $product['product_size']; ?>">
                        <?= $product['name']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <input type="text" name="products[0][size]" placeholder="Size" class="form-control" required>
            <input type="number" name="products[0][quantity]" placeholder="Qty" class="form-control quantity" required>
            <input type="number" name="products[0][price]" placeholder="Price" class="form-control price" required>
            <input type="text" class="form-control subtotal" placeholder="Subtotal" readonly>
            <button type="button" class="btn btn-remove" onclick="removeProduct(this)">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `);

    // Reset Transposition Form
    document.getElementById('transpositionForm').reset();

    // Hide dynamic sections if required
    $('#customerDetails').hide();
    $('#customerNotFound').addClass('d-none');

    // Reset amount fields
    $('#totalQuantity, #totalAmount, #tax, #discount, #finalAmount, #amountInWords').val('');
}

// ✅ Redirect to print the invoice and reset forms
function redirectToPrint() {
    // Close the modal
    var invoiceModal = bootstrap.Modal.getInstance(document.getElementById('invoiceModal'));
    invoiceModal.hide();

    // Reset all forms
    resetForms();

    // Open the print window
    if (lastInvoiceId) {
        window.open(`printinvoice.php?invoice_id=${lastInvoiceId}`, '_blank');
    } else {
        alert("Invoice ID is missing!");
    }
}



// ✅ Ensure "Customer Not Found" alert is hidden on page load
document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("customerNotFound").classList.add("d-none"); // Ensure alert is hidden at start
});

document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("tax").value = 0;
    document.getElementById("discount").value = 0;
    calculateTotals();
    checkProductCount();
    toggleInvoiceSection(false); // Initially hide invoice form
});

// 📊 Function to Calculate Totals
function calculateTotals() {
    let totalQuantity = 0;
    let totalAmount = 0;

    document.querySelectorAll(".product-group").forEach((group) => {
        let quantity = parseFloat(group.querySelector(".quantity").value) || 0;
        let price = parseFloat(group.querySelector(".price").value) || 0;
        let subtotal = quantity * price;

        group.querySelector(".subtotal").value = subtotal.toFixed(2);
        totalQuantity += quantity;
        totalAmount += subtotal;
    });

    let tax = parseFloat(document.getElementById("tax").value) || 0;
    let discount = parseFloat(document.getElementById("discount").value) || 0;
    let discountAmount = (totalAmount * discount) / 100;
    let finalAmount = totalAmount + tax - discountAmount;

    document.getElementById("totalQuantity").value = totalQuantity;
    document.getElementById("totalAmount").value = totalAmount.toFixed(2);
    document.getElementById("finalAmount").value = finalAmount.toFixed(2);
    document.getElementById("amountInWords").value = convertToWords(finalAmount);
    document.getElementById('amount_in_words').value = convertToWords(finalAmount);
}


// 🧮 Function to Calculate Subtotal
function updateSubtotal(element) {
    let parent = element.closest(".product-group");
    let quantity = parseFloat(parent.querySelector(".quantity").value) || 0;
    let price = parseFloat(parent.querySelector(".price").value) || 0;
    let subtotal = quantity * price;

    parent.querySelector(".subtotal").value = subtotal.toFixed(2);
    calculateTotals(); // Update overall totals
}


// ➕ Function to Add New Product Row
let productCount = 1;
function addProduct() {
    let productSection = document.getElementById("product-section");

    let newProduct = document.createElement("div");
    newProduct.classList.add("product-group", "d-flex", "gap-3", "mb-2");
    newProduct.innerHTML = `
        <select name="products[${productCount}][id]" class="form-select product-select" required onchange="updateSize(this)">
            <option value="" disabled selected>Select Product</option>
            ${document.querySelector("select[name='products[0][id]']").innerHTML}
        </select>
      <input type="text" name="products[${productCount}][size]" placeholder="Size" class="form-control product-size" required>
        <input type="number" name="products[${productCount}][quantity]" placeholder="Qty" class="form-control quantity" required>
        <input type="number" name="products[${productCount}][price]" placeholder="Price" class="form-control price" required>
        <input type="text" class="form-control subtotal" placeholder="Subtotal" readonly>
        <button type="button" class="btn btn-remove" onclick="removeProduct(this)"><i class="fas fa-trash"></i></button>
    `;

    productSection.appendChild(newProduct);
    productCount++;
    checkProductCount();
    calculateTotals(); // Recalculate after adding
}


// ❌ Function to Remove Product Row
function removeProduct(button) {
    button.closest(".product-group").remove();
    checkProductCount();
    calculateTotals();
}

// 🔄 Ensure At Least One Product is Always Present
function checkProductCount() {
    let productRows = document.querySelectorAll("#product-section .product-group");
    let deleteButtons = document.querySelectorAll(".btn-remove");

    if (productRows.length === 1) {
        deleteButtons[0].style.display = "none"; // Hide delete button if only one product exists
    } else {
        deleteButtons.forEach(btn => btn.style.display = "inline-flex"); // Show delete button for all
    }
}

// 🔢 Function to Convert Number to Words
function convertToWords(num) {
    if (num === 0) return "Zero Rupees Only";

    const a = ["", "One", "Two", "Three", "Four", "Five", "Six", "Seven", "Eight", "Nine", "Ten",
               "Eleven", "Twelve", "Thirteen", "Fourteen", "Fifteen", "Sixteen", "Seventeen", "Eighteen", "Nineteen"];
    const b = ["", "", "Twenty", "Thirty", "Forty", "Fifty", "Sixty", "Seventy", "Eighty", "Ninety"];

    function convert(n) {
        if (n < 20) return a[n];
        if (n < 100) return b[Math.floor(n / 10)] + " " + a[n % 10];
        if (n < 1000) return a[Math.floor(n / 100)] + " Hundred " + convert(n % 100);
        if (n < 100000) return convert(Math.floor(n / 1000)) + " Thousand " + convert(n % 1000);
        if (n < 10000000) return convert(Math.floor(n / 100000)) + " Lakh " + convert(n % 100000);
        return "Number Too Large";
    }

    return convert(Math.floor(num)) + " Rupees Only /-";
}




// 🏷 Automatically Update Product Size When Product is Selected
function updateSize(selectElement) {
    let selectedOption = selectElement.options[selectElement.selectedIndex];
    let sizeInput = selectElement.closest(".product-group").querySelector(".product-size");
    sizeInput.value = selectedOption.dataset.size || "";
}



function toggleTransactionField() {
    const paymentMethod = document.getElementById('paymentMethod').value;
    const transactionField = document.getElementById('transactionNumberField');
    
    if (paymentMethod === 'UPI' || paymentMethod === 'Bank Transfer') {
        transactionField.classList.remove('d-none');
    } else {
        transactionField.classList.add('d-none');
        document.getElementById('transactionNo').value = ''; // Clear value if hidden
    }
}



// Initialize field visibility on page load
document.addEventListener('DOMContentLoaded', toggleTransactionField);
