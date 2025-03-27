<?php 
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

include 'config/db.php';
include 'sidebar.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $gstn = $_POST['gstn'];
    $address = $_POST['address'];
    $pincode = $_POST['pincode'];
    $district = $_POST['district'];
    $state = $_POST['state'];

    $stmt = $conn->prepare("INSERT INTO customers (name, phone, email, gstn, address, pincode, district, state, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("ssssssss", $name, $phone, $email, $gstn, $address, $pincode, $district, $state);

    if ($stmt->execute()) {
        header("Location: customers.php");
        exit();
    } else {
        echo "<script>alert('Error adding customer');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Customer - Billing System</title>

    <style>
        body {
            background: linear-gradient(135deg, #f0f4ff, #d9e4f5);
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 770px;
            margin-left:350px;
            padding: 20px;
            animation: fadeIn 0.8s ease-in-out;
        }

        .card {
            background-color: #fff;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
            animation: slideIn 1s ease;
        }

        .form-label {
            font-weight: bold;
            color: #555;
        }

        input[type="text"], input[type="email"], textarea {
            width: 100%;
            padding: 14px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 8px;
            transition: border-color 0.3s, transform 0.2s ease;
        }

        input[type="text"]:focus, input[type="email"]:focus, textarea:focus {
            border-color: #28a745;
            transform: scale(1.02);
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

        .btn-success {
            background-color: #28a745;
            color: #fff;
        }

        .btn-success:hover {
            background-color: #218838;
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

        .mt-4 {
            margin-top: 1.5rem;
        }

        .mb-3 {
            margin-bottom: 1.3rem;
        }

        /* Animations */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @media (max-width: 600px) {
            .container {
                padding: 15px;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="card shadow-sm">
        <h2>➕ Add New Customer</h2>
        <form method="POST" class="mt-4">

            <div class="mb-3">
                <label class="form-label">Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Phone</label>
                <input type="text" name="phone" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control">
            </div>

            <div class="mb-3">
                <label class="form-label">GSTN</label>
                <input type="text" name="gstn" class="form-control">
            </div>

            <div class="mb-3">
                <label class="form-label">Address</label>
                <textarea name="address" class="form-control" rows="3" required></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Pincode</label>
                <input type="text" name="pincode" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">District</label>
                <input type="text" name="district" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">State</label>
                <input type="text" name="state" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-success">➕ Add Customer</button>
            <a href="customers.php" class="btn btn-secondary">❌ Cancel</a>
        </form>
    </div>
</div>

</body>
</html>
