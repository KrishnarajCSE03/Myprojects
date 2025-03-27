<?php 
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

include 'config/db.php';
include 'sidebar.php';

// Delete Customer
if (isset($_GET['delete_id'])) {
    $customer_id = $_GET['delete_id'];

    // Disable foreign key checks temporarily to allow deletion
    $conn->query("SET FOREIGN_KEY_CHECKS = 0");

    // Perform the deletion
    $conn->query("DELETE FROM customers WHERE customer_id = $customer_id");

    // Re-enable foreign key checks to ensure data integrity
    $conn->query("SET FOREIGN_KEY_CHECKS = 1");

    // Redirect back to customers page
    header("Location: customers.php");
    exit();
}

// Fetch Customers
$customers = $conn->query("SELECT * FROM customers ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customers - Billing System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background: #f0f4ff;
            font-family: 'Poppins', sans-serif;
            margin: 0;
        }

        .main-content {
            margin-left: 270px;
            padding: 30px;
            transition: margin-left 0.3s ease;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .btn-custom {
            padding: 10px 16px;
            border: none;
            border-radius: 8px;
            background: #28A745;
            color: #fff;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .btn-custom:hover {
            background: #218838;
            transform: scale(1.05);
        }

        .cards-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .customer-card {
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            position: relative;
        }

        .customer-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }

        .customer-card h5 {
            margin-bottom: 10px;
            font-weight: bold;
            color: #333;
        }

        .customer-info p {
            margin: 2px 0;
            color: #555;
            font-size: 14px;
        }

        .action-buttons {
            margin-top: 15px;
            display: flex;
            justify-content: space-between;
        }

        .btn-blue, .btn-red {
            padding: 8px 12px;
            border: none;
            border-radius: 6px;
            color: #fff;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-blue {
            background: #007BFF;
        }

        .btn-blue:hover {
            background: #0069d9;
        }

        .btn-red {
            background: #DC3545;
        }

        .btn-red:hover {
            background: #c82333;
        }

        @media (max-width: 1024px) {
            .main-content {
                margin-left: 80px;
            }
        }

        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                gap: 15px;
            }

            .main-content {
                margin-left: 60px;
                padding: 20px;
            }
        }

        @media (max-width: 500px) {
            .main-content {
                margin-left: 0;
                padding-top: 100px;
            }
        }
    </style>
</head>
<body>

<div class="main-content">
    <div class="header">
        <h2><i class="bi bi-people-fill"></i> Customer Management</h2>
        <a href="add_customer.php" class="btn-custom">
            <i class="bi bi-plus-circle"></i> Add Customer
        </a>
    </div>

    <div class="cards-container">
        <?php while ($row = $customers->fetch_assoc()) { ?>
        <div class="customer-card">
            <h5><?php echo htmlspecialchars($row['name']); ?></h5>
            <div class="customer-info">
                <p><strong>Phone:</strong> <?php echo htmlspecialchars($row['phone']); ?></p>
                <p><strong>Address:</strong> <?php echo htmlspecialchars($row['address']); ?></p>
                <p><strong>Pincode:</strong> <?php echo htmlspecialchars($row['pincode']); ?></p>
                <p><strong>District:</strong> <?php echo htmlspecialchars($row['district']); ?></p>
                <p><strong>State:</strong> <?php echo htmlspecialchars($row['state']); ?></p>
                <p><strong>GSTN:</strong> <?php echo htmlspecialchars($row['gstn'] ?? 'Not Available'); ?></p>
            </div>
            <div class="action-buttons">
                <a href="edit_customer.php?id=<?php echo $row['customer_id']; ?>" class="btn-blue">
                    <i class="bi bi-pencil-square"></i> Edit
                </a>
                <a href="customers.php?delete_id=<?php echo $row['customer_id']; ?>" class="btn-red" onclick="return confirm('Are you sure you want to delete this customer?');">
                    <i class="bi bi-trash"></i> Delete
                </a>
            </div>
        </div>
        <?php } ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
