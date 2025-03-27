<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

include 'config/db.php';
include 'sidebar.php';

$customer_id = $_GET['id'] ?? null;

if (!$customer_id) {
    header("Location: customers.php");
    exit();
}

// Fetch existing customer details
$stmt = $conn->prepare("SELECT * FROM customers WHERE customer_id = ?");
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();
$customer = $result->fetch_assoc();

if (!$customer) {
    echo "<script>alert('Customer not found'); window.location='customers.php';</script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $gstn = $_POST['gstn'];
    $address = $_POST['address'];
    $pincode = $_POST['pincode'];
    $district = $_POST['district'];
    $state = $_POST['state'];

    $update_stmt = $conn->prepare("UPDATE customers SET name = ?, phone = ?, email = ?, gstn = ?, address = ?, pincode = ?, district = ?, state = ?, updated_at = NOW() WHERE customer_id = ?");
    $update_stmt->bind_param("ssssssssi", $name, $phone, $email, $gstn, $address, $pincode, $district, $state, $customer_id);

    if ($update_stmt->execute()) {
        header("Location: customers.php");
        exit();
    } else {
        echo "<script>alert('Error updating customer');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Customer - Billing System</title>

    <style>
        body {
            background: linear-gradient(135deg, #f0f4ff, #d9e4f5);
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
        }

        .modal {
            width: calc(100% - 250px);
            height: 160vh;
            background-color: rgba(0, 0, 0, 0.5);
            margin-left:250px;
            top: 100;
            left: 250px;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            overflow-y: auto;
            box-sizing: border-box;
        }

        .modal-content {
            background-color: #fff;
            padding: 30px;
            border-radius: 12px;
            width: 100%;
            max-width: 600px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            margin: 20px 0;
            animation: slideIn 0.7s ease;
        }

        h2 {
            text-align: center;
            color: #2a2e35;
            margin-bottom: 20px;
            font-size: 24px;
        }

        .form-label {
            font-weight: bold;
            color: #3b3f45;
            margin-top: 15px;
            display: block;
        }

        input, textarea {
            width: 100%;
            padding: 14px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-size: 16px;
        }

        input:focus, textarea:focus {
            border-color: #17a2b8;
            box-shadow: 0 0 12px rgba(23, 162, 184, 0.3);
            outline: none;
        }

        textarea {
            resize: vertical;
        }

        .btn {
            padding: 14px 22px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background-color: #17a2b8;
            color: #fff;
        }

        .btn-primary:hover {
            background-color: #138496;
            transform: scale(1.05);
        }

        .btn-secondary {
            background-color: #6c757d;
            color: #fff;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
            transform: scale(1.05);
        }

        .btn-wrapper {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
        }

        @keyframes slideIn {
            from {
                transform: translateY(20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @media (max-width: 600px) {
            .modal-content {
                padding: 20px;
            }

            .btn {
                width: 100%;
                margin-bottom: 10px;
            }

            .btn-wrapper {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>

<div class="modal">
    <div class="modal-content">
        <h2>✏️ Edit Customer</h2>
        <form method="POST">

            <label class="form-label">Name</label>
            <input type="text" name="name" value="<?= htmlspecialchars($customer['name']) ?>" required>

            <label class="form-label">Phone</label>
            <input type="text" name="phone" value="<?= htmlspecialchars($customer['phone']) ?>" required>

            <label class="form-label">Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($customer['email']) ?>">

            <label class="form-label">GSTN</label>
            <input type="text" name="gstn" value="<?= htmlspecialchars($customer['gstn']) ?>">

            <label class="form-label">Address</label>
            <textarea name="address" rows="3" required><?= htmlspecialchars($customer['address']) ?></textarea>

            <label class="form-label">Pincode</label>
            <input type="text" name="pincode" value="<?= htmlspecialchars($customer['pincode']) ?>" required>

            <label class="form-label">District</label>
            <input type="text" name="district" value="<?= htmlspecialchars($customer['district']) ?>" required>

            <label class="form-label">State</label>
            <input type="text" name="state" value="<?= htmlspecialchars($customer['state']) ?>" required>

            <div class="btn-wrapper">
                <button type="submit" class="btn btn-primary">✅ Update Customer</button>
                <a href="customers.php" class="btn btn-secondary">❌ Cancel</a>
            </div>

        </form>
    </div>
</div>

</body>
</html>
