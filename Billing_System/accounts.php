<?php include 'sidebar.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Accounts Summary</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    
    <!-- ✅ SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background: linear-gradient(135deg, #f0f4ff, #d9e4f5);
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
        }

        .content {
            margin-left: 280px;
            padding: 20px;
        }

        .card {
            background-color: #fff;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        h2, h3 {
            color: #2c3e50;
            margin-bottom: 20px;
        }

        .filter-section {
            display: flex;
            justify-content: space-between;
            gap: 15px;
            margin-bottom: 20px;
            align-items: center;
        }

        select, input[type="date"] {
            padding: 10px 15px;
            border: 1px solid #ccc;
            border-radius: 6px;
            outline: none;
            font-size: 14px;
        }

        .summary-card {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            flex-wrap: wrap;
        }

        .summary-item {
            background: linear-gradient(135deg, #6ab04c, #badc58);
            padding: 18px 25px;
            border-radius: 10px;
            width: 23%;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            text-align: center;
            color: #fff;
            font-weight: bold;
            font-size: 16px;
        }

        .sales-table {
            width: 100%;
            border-collapse: collapse;
        }

        .sales-table th, .sales-table td {
            padding: 14px;
            border-bottom: 1px solid #ccc;
            text-align: center;
            font-size: 14px;
        }

        .btn {
            padding: 12px 18px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            margin-top: 10px;
            transition: 0.3s;
            font-weight: bold;
            font-size: 14px;
        }

        .btn-primary {
            background-color: #27ae60;
            color: #fff;
        }

        .btn-primary:hover {
            background-color: #1e8449;
        }

        .btn-danger {
            background-color: #e74c3c;
            color: #fff;
        }

        .btn-danger:hover {
            background-color: #c0392b;
        }

        .icon {
            margin-right: 5px;
        }

    </style>
</head>
<body>

<div class="content">
    <div class="card">
        <h2><i class="fas fa-chart-line icon"></i> Account Summary</h2>

        <div class="filter-section">
            <div>
                <input type="date" id="from_date" placeholder="From Date">
                <input type="date" id="to_date" placeholder="To Date">
                <button class="btn btn-primary" onclick="filterAccounts()">
                    <i class="fas fa-filter icon"></i>Filter
                </button>
            </div>

            <div>
                <select id="fileFormat" required>
                    <option value="" disabled selected>📄 Select Format</option>
                    <option value="csv">CSV</option>
                    <option value="excel">Excel</option>
                    
                </select>
            </div>
        </div>

        <div class="summary-card">
            <div class="summary-item" id="total-sales"><i class="fas fa-money-bill-wave icon"></i> Total Sales: ₹0</div>
            <div class="summary-item" id="total-tax"><i class="fas fa-percent icon"></i> Total Tax: ₹0</div>
            <div class="summary-item" id="total-discount"><i class="fas fa-tags icon"></i> Total Discount: ₹0</div>
            <div class="summary-item" id="final-amount"><i class="fas fa-wallet icon"></i> Final Amount: ₹0</div>
        </div>

        <h3><i class="fas fa-file-alt icon"></i> Sales Report</h3>
        <table class="sales-table" id="salesReport">
            <thead>
                <tr>
                    <th>Invoice ID</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th>Total Amount</th>
                    <th>Tax</th>
                    <th>Discount</th>
                    <th>Final Amount</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>

        <button class="btn btn-primary" onclick="downloadReport()">
            <i class="fas fa-download icon"></i>Download Report
        </button>
        <button class="btn btn-danger" onclick="confirmTruncate()">
            <i class="fas fa-lock icon"></i>Close Account
        </button>
    </div>
</div>

<script>
    function filterAccounts() {
        const fromDate = document.getElementById('from_date').value;
        const toDate = document.getElementById('to_date').value;

        if (!fromDate || !toDate) {
            alert("Please select both from and to dates.");
            return;
        }

        fetch(`fetch_accounts.php?from=${fromDate}&to=${toDate}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('total-sales').innerText = `Total Sales: ₹${data.total_sales}`;
                document.getElementById('total-tax').innerText = `Total Tax: ₹${data.total_tax}`;
                document.getElementById('total-discount').innerText = `Total Discount: ₹${data.total_discount}`;
                document.getElementById('final-amount').innerText = `Final Amount: ₹${data.final_amount}`;

                const tbody = document.querySelector('#salesReport tbody');
                tbody.innerHTML = '';
                if (data.sales && data.sales.length > 0) {
                    data.sales.forEach(sale => {
                        tbody.innerHTML += `
                            <tr>
                                <td>${sale.invoice_id}</td>
                                <td>${sale.customer}</td>
                                <td>${sale.date}</td>
                                <td>₹${sale.total_amount}</td>
                                <td>₹${sale.tax}</td>
                                <td>₹${sale.discount}</td>
                                <td>₹${sale.final_amount}</td>
                            </tr>`;
                    });
                } else {
                    tbody.innerHTML = `<tr><td colspan="7">No data found for the selected dates.</td></tr>`;
                }
            });
    }

    function downloadReport() {
        const format = document.getElementById('fileFormat').value;
        const fromDate = document.getElementById('from_date').value;
        const toDate = document.getElementById('to_date').value;

        if (!format || !fromDate || !toDate) {
            alert("Please select a file format and date range.");
            return;
        }

        window.location.href = `download_report.php?format=${format}&from=${fromDate}&to=${toDate}`;
    }

    function confirmTruncate() {
        Swal.fire({
            title: 'Are you sure?',
            text: "This will clear all invoices and start a new year!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, clear it!',
            cancelButtonText: 'No, cancel!',
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('truncate_invoices.php', {
                    method: 'POST',
                })
                .then(response => response.json())
                .then(data => {
                    Swal.fire({
                        icon: data.status === 'success' ? 'success' : 'error',
                        title: data.status === 'success' ? 'Cleared!' : 'Error!',
                        text: data.message,
                    });
                })
                .catch(error => {
                    Swal.fire('Error', 'Something went wrong: ' + error.message, 'error');
                });
            }
        });
    }



</script>

</body>
</html>
