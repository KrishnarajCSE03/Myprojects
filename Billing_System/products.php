<?php 
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

include 'config/db.php';
include 'sidebar.php';

// Delete Product
if (isset($_GET['delete_id'])) {
    $product_id = $_GET['delete_id'];
    $conn->query("DELETE FROM products WHERE product_id = $product_id");
    header("Location: products.php");
    exit();
}

$products = $conn->query("SELECT * FROM products ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Products - Billing System</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f0f4ff, #d9e4f5);
            padding: 20px;
            margin: 0;
        }
        .main-content {
            max-width: 1000px;
            margin-left:280px;
            background: #fff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
            animation: fadeIn 1s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }
        .header h2 {
            color: #333;
            font-size: 26px;
        }
        .btn-custom {
            background: linear-gradient(90deg, #4e54c8, #8f94fb);
            color: #fff;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .btn-custom:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.3);
        }
        .content-box h4 {
            margin-bottom: 15px;
            color: #555;
            font-weight: 500;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        th, td {
            padding: 15px;
            text-align: left;
        }
        th {
            background: #4e54c8;
            color: #fff;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 14px;
        }
        tr {
            transition: background-color 0.3s ease;
        }
        tr:hover {
            background-color: #f1f5ff;
        }
        td {
            color: #555;
            font-size: 14px;
            border-bottom: 1px solid #eee;
        }
        .actions a {
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
            margin-right: 5px;
            display: inline-flex;
            align-items: center;
            transition: transform 0.2s;
        }
        .actions a i {
            margin-right: 5px;
        }
        .btn-info {
            background: #00b4d8;
            color: white;
        }
        .btn-info:hover {
            background: #0096c7;
            transform: scale(1.05);
        }
        .btn-danger {
            background: #e63946;
            color: white;
        }
        .btn-danger:hover {
            background: #d62839;
            transform: scale(1.05);
        }
    </style>
</head>
<body>

<div class="main-content">
    <div class="header">
        <h2><i class="bi bi-box-seam"></i> Products</h2>
        <a href="add_products.php" class="btn-custom">
            <i class="bi bi-plus-circle"></i> Add Product
        </a>
    </div>

    <div class="content-box">
        <h4><i class="bi bi-card-list"></i> All Products</h4>
        <table>
            <thead>
                <tr>
                    
                    <th>Product Name</th>
                    <th>Size</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $products->fetch_assoc()) { ?>
                <tr>
                    
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['product_size']; ?></td>
                    <td><?php echo $row['created_at']; ?></td>
                    <td class="actions">
                        <a href="edit_product.php?id=<?php echo $row['product_id']; ?>" class="btn-info">
                            <i class="bi bi-pencil-square"></i> Edit
                        </a>
                        <a href="products.php?delete_id=<?php echo $row['product_id']; ?>" class="btn-danger" onclick="return confirm('Are you sure you want to delete this product?');">
                            <i class="bi bi-trash"></i> Delete
                        </a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
