<?php 
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

include 'config/db.php';
include 'sidebar.php';

// Fetching Admin Dashboard Data
$total_revenue = $conn->query("SELECT SUM(final_amount) AS total FROM invoices WHERE status='paid'")->fetch_assoc()['total'] ?? 0;
$total_invoices = $conn->query("SELECT COUNT(*) AS total FROM invoices")->fetch_assoc()['total'] ?? 0;
$total_monthly_revenue = $conn->query("SELECT SUM(final_amount) AS total FROM invoices WHERE status='paid' AND MONTH(created_at) = MONTH(CURRENT_DATE()) AND YEAR(created_at) = YEAR(CURRENT_DATE())")->fetch_assoc()['total'] ?? 0;
$total_yearly_revenue = $conn->query("SELECT SUM(final_amount) AS total FROM invoices WHERE status='paid' AND YEAR(created_at) = YEAR(CURRENT_DATE())")->fetch_assoc()['total'] ?? 0;
$total_pending_invoices = $conn->query("SELECT COUNT(*) AS total FROM invoices WHERE status='pending'")->fetch_assoc()['total'] ?? 0;
$total_payments = $conn->query("SELECT SUM(amount_paid) AS total FROM payments")->fetch_assoc()['total'] ?? 0;

// Monthly Revenue Data for Chart
$monthly_revenue_data = [];
$monthly_bills_data = [];
$labels = [];
for ($i = 1; $i <= 12; $i++) {
    $revenue = $conn->query("SELECT SUM(final_amount) AS total FROM invoices WHERE status='paid' AND MONTH(created_at) = $i AND YEAR(created_at) = YEAR(CURRENT_DATE())")->fetch_assoc()['total'] ?? 0;
    $bills = $conn->query("SELECT COUNT(*) AS total FROM invoices WHERE MONTH(created_at) = $i AND YEAR(created_at) = YEAR(CURRENT_DATE())")->fetch_assoc()['total'] ?? 0;
    $monthly_revenue_data[] = $revenue;
    $monthly_bills_data[] = $bills;
    $labels[] = date("M", mktime(0, 0, 0, $i, 1));
}

// Day-wise Revenue and Bills Data (Last 7 Days)
$day_labels = [];
$day_revenue_data = [];
$day_bills_data = [];

for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $day_labels[] = date('D', strtotime($date));
    $revenue = $conn->query("SELECT SUM(final_amount) AS total FROM invoices WHERE status='paid' AND DATE(created_at) = '$date'")->fetch_assoc()['total'] ?? 0;
    $bills = $conn->query("SELECT COUNT(*) AS total FROM invoices WHERE DATE(created_at) = '$date'")->fetch_assoc()['total'] ?? 0;
    $day_revenue_data[] = $revenue;
    $day_bills_data[] = $bills;
}

// Fetching Recent Transactions
$recent_transactions = $conn->query("
    SELECT invoices.invoice_id, customers.name AS customer_name, invoices.final_amount, invoices.status 
    FROM invoices
    JOIN customers ON invoices.customer_id = customers.customer_id
    ORDER BY invoices.created_at DESC 
    LIMIT 5
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Billing System</title>
    <link rel="stylesheet" href="assets/dashboardstyle.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<div class="main-content">
    <div class="header">
        <h2>📊 M/s. RAKSHINI TRADERS, MADURAI</h2>
        <div class="profile">
            <span>Welcome, <?php echo $_SESSION['role']; ?>!</span>
            <img src="assets/logo.png" alt="Profile">
        </div>
    </div>

    <!-- 📊 Dashboard Statistics Cards -->
    <div class="stats">
        <div class="card card-primary"><h5>Total Revenue</h5><h3>₹<?php echo number_format($total_revenue, 2); ?></h3></div>
        <div class="card card-success"><h5>Total Bills</h5><h3><?php echo $total_invoices; ?></h3></div>
        <div class="card card-warning"><h5>📅 Monthly Revenue</h5><h3>₹<?php echo number_format($total_monthly_revenue, 2); ?></h3></div>
        <div class="card card-danger"><h5>🚨 Pending Bills</h5><h3><?php echo $total_pending_invoices; ?></h3></div>
        <div class="card card-info"><h5>📆 Yearly Revenue</h5><h3>₹<?php echo number_format($total_yearly_revenue, 2); ?></h3></div>
    </div>

    <!-- 📈 Charts Section -->
    <div class="charts-container">
    <!-- Monthly Charts at the Top -->
    <div class="chart-box">
        <h4>📈 Monthly Revenue</h4>
        <canvas id="monthlyRevenueChart"></canvas>
    </div>
    <div class="chart-box">
        <h4>📊 Monthly Bills Count</h4>
        <canvas id="monthlyBillsChart"></canvas>
    </div>

    <!-- Day-wise Charts Below -->
    <div class="chart-box">
        <h4>📅 Last 7 Days Revenue</h4>
        <canvas id="dayRevenueChart"></canvas>
    </div>
    <div class="chart-box">
        <h4>🧾 Last 7 Days Bills Count</h4>
        <canvas id="dayBillsChart"></canvas>
    </div>
</div>


    
    <!-- 📋 Recent Transactions Table -->
    <div class="content-box">
        <h4>📋 Recent Transactions</h4>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Invoice #</th>
                    <th>Customer</th>
                    <th>Amount</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $recent_transactions->fetch_assoc()) { ?>
                <tr>
                    <td>#<?php echo $row['invoice_id']; ?></td>
                    <td><?php echo $row['customer_name']; ?></td>
                    <td>₹<?php echo number_format($row['final_amount'], 2); ?></td>
                    <td style="color: <?php echo ($row['status'] === 'paid') ? 'green' : 'red'; ?>;">
                        <?php echo ucfirst($row['status']); ?>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <!-- 🎯 Quick Actions -->
    <div class="actions">
        <button class="btn-blue" onClick="myFunction1()">➕ Manage Customer</button>
        <button class="btn-green" onclick="myFunction()">➕ Add Invoice</button>
        <button class="btn-orange" onclick="myFunction2()">📊 View Reports</button>
    </div>
</div>

<script>
// 📊 Monthly Revenue Chart
var ctx1 = document.getElementById("monthlyRevenueChart").getContext("2d");
new Chart(ctx1, {
    type: "bar",
    data: {
        labels: <?php echo json_encode($labels); ?>,
        datasets: [{
            label: "Revenue (₹)",
            data: <?php echo json_encode($monthly_revenue_data); ?>,
            backgroundColor: "rgba(0, 123, 255, 0.7)",
            borderColor: "#0056D2",
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// 📊 Monthly Bills Count Chart
var ctx2 = document.getElementById("monthlyBillsChart").getContext("2d");
new Chart(ctx2, {
    type: "line",
    data: {
        labels: <?php echo json_encode($labels); ?>,
        datasets: [{
            label: "Bills Count",
            data: <?php echo json_encode($monthly_bills_data); ?>,
            backgroundColor: "rgba(255, 99, 132, 0.6)",
            borderColor: "#DC3545",
            borderWidth: 2,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

var ctx3 = document.getElementById("dayRevenueChart").getContext("2d");
new Chart(ctx3, {
    type: "bar",
    data: {
        labels: <?php echo json_encode($day_labels); ?>,
        datasets: [{
            label: "Daily Revenue (₹)",
            data: <?php echo json_encode($day_revenue_data); ?>,
            backgroundColor: "rgba(75, 192, 192, 0.7)",
            borderColor: "#17A2B8",
            borderWidth: 2
        }]
    }
});

// 🧾 Last 7 Days Bills Count Chart
var ctx4 = document.getElementById("dayBillsChart").getContext("2d");
new Chart(ctx4, {
    type: "line",
    data: {
        labels: <?php echo json_encode($day_labels); ?>,
        datasets: [{
            label: "Bills Count",
            data: <?php echo json_encode($day_bills_data); ?>,
            backgroundColor: "rgba(153, 102, 255, 0.6)",
            borderColor: "#6F42C1",
            borderWidth: 2,
            fill: true
        }]
    }
});


        function myFunction() {
            // Redirect to the add_invoice page when the button is clicked
            window.location.href = "add_invoice.php";
        }
        function myFunction1() {
            // Redirect to the add_invoice page when the button is clicked
            window.location.href = "customers.php";
        }
        function myFunction2() {
            // Redirect to the add_invoice page when the button is clicked
            window.location.href = "accounts.php";
        }
    
</script>

</body>
</html>
