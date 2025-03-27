<?php 
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

include 'config/db.php';
include 'sidebar.php';

$product_id = $_GET['id'] ?? null;

if (!$product_id) {
    header("Location: products.php");
    exit();
}

// Fetch existing product details
$stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    echo "<script>alert('Product not found'); window.location='products.php';</script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $size = $_POST['product_size'];

    $update_stmt = $conn->prepare("UPDATE products SET name = ?, product_size = ?, updated_at = NOW() WHERE product_id = ?");
    $update_stmt->bind_param("ssi", $name, $size, $product_id);

    if ($update_stmt->execute()) {
        header("Location: products.php");
        exit();
    } else {
        echo "<script>alert('Error updating product');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - Billing System</title>
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #f0f4ff, #d9e4f5);
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin-left:350px;
            padding: 20px;
            animation: fadeIn 0.8s ease-in-out;
        }

        .card {
            background-color: #fff;
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-8px);
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 25px;
        }

        .input-group {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .input-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #888;
        }

        input {
            width: 100%;
            padding: 14px 18px 14px 45px;
            border: 1px solid #ccc;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-size: 16px;
        }

        input:focus {
            border-color: #28a745;
            box-shadow: 0 0 10px rgba(40, 167, 69, 0.2);
            outline: none;
        }

        .btn {
            padding: 14px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            transition: transform 0.3s, background-color 0.3s;
        }

        .btn-success {
            background-color: #28a745;
            color: #fff;
        }

        .btn-success:hover {
            background-color: #218838;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background-color: #6c757d;
            color: #fff;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
            transform: translateY(-2px);
        }

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

    </style>
</head>
<body>

<div class="container">
    <div class="card">
        <h2>✏️ Edit Product</h2>
        <form method="POST">

            <div class="input-group">
                <i class="fa fa-box"></i>
                <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" placeholder="Product Name" required>
            </div>

            <div class="input-group">
                <i class="fa fa-ruler-combined"></i>
                <input type="text" name="product_size" value="<?= htmlspecialchars($product['product_size']) ?>" placeholder="Product Size" required>
            </div>

            <button type="submit" class="btn btn-success">✅ Update Product</button>
            <a href="products.php" class="btn btn-secondary">❌ Cancel</a>

        </form>
    </div>
</div>

</body>
</html>
