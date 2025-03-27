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
    $prdsiz = $_POST['product_size'];

    $stmt = $conn->prepare("INSERT INTO products (name, product_size, created_at) VALUES (?, ?, NOW())");
    $stmt->bind_param("ss", $name, $prdsiz);

    if ($stmt->execute()) {
        header("Location: products.php");
        exit();
    } else {
        echo "<script>alert('Error adding product');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Product - Billing System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f0f4ff, #d9e4f5);
            padding: 30px;
        }

        /* Sidebar Fix */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            width: 250px;
            background: linear-gradient(135deg, #f0f4ff, #d9e4f5);
            color: #fff;
            z-index: 1000;
            overflow-y: auto;
        }

        .main-content {
            margin-left: 260px; /* Space for the sidebar */
            transition: margin 0.3s ease;
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
            animation: fadeIn 0.8s ease-in-out;
            z-index: 10;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        h2 {
            color: #4e54c8;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .form-label {
            font-weight: 500;
            color: #555;
        }

        .form-control {
            border-radius: 8px;
            border: 1px solid #ccc;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #4e54c8;
            box-shadow: 0 0 10px rgba(78, 84, 200, 0.3);
        }

        .btn-custom {
            background: linear-gradient(90deg, #4e54c8, #8f94fb);
            color: #fff;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            border: none;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .btn-custom:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.3);
        }

        .btn-secondary {
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .btn-secondary:hover {
            background-color: #6c757d;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <?php include 'sidebar.php'; ?>
</div>

<!-- Main Content -->
<div class="main-content">
    <div class="container mt-4">
        <div class="card shadow-sm p-4">
            <h2><i class="bi bi-box-seam"></i> Add New Product</h2>
            <form method="POST" class="mt-4">
                <div class="mb-3">
                    <label class="form-label"><i class="bi bi-tag-fill"></i> Product Name</label>
                    <input type="text" name="name" class="form-control" placeholder="Enter product name" required>
                </div>
                <div class="mb-3">
                    <label class="form-label"><i class="bi bi-arrows-fullscreen"></i> Product Size</label>
                    <input type="text" name="product_size" class="form-control" placeholder="Enter product size" required>
                </div>
                <button type="submit" class="btn btn-custom">
                    <i class="bi bi-plus-circle"></i> Add Product
                </button>
                <a href="products.php" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Cancel
                </a>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
