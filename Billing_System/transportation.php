<?php
include 'config/db.php';
include 'sidebar.php'; // Sidebar & Navigation
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Transportation</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Nunito', sans-serif;
            background: linear-gradient(135deg, #f0f4ff, #d9e4f5);
        }
        .container {
            background: #ffffff;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 6px 25px rgba(0, 0, 0, 0.15);
            margin-top: 30px;
            margin-left: 265px;
            
        h2 {
            color: #2d3436;
            font-weight: 700;
        }
        p {
            color: #636e72;
        }
        .table-container {
            margin-top: 25px;
        }
        .table {
            border-collapse: separate;
            border-spacing: 0 12px;
        }
        .table th {
            background-color: #4e73df;
            color: #fff;
            text-align: center;
            padding: 15px;
            border-radius: 8px;
            font-size: 15px;
            letter-spacing: 1px;
        }
        .table td {
            background-color: #ffffff;
            padding: 15px;
            text-align: center;
            vertical-align: middle;
            border-radius: 8px;
            transition: all 0.4s ease;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }
        .table tr:hover td {
            background: #f1f4f9;
            transform: translateY(-3px);
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
        }
        .badge {
            padding: 10px 14px;
            font-size: 0.85rem;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .badge.delivered {
            background: linear-gradient(135deg, #28a745, #218838);
            color: #fff;
        }
        .badge.pending {
            background: linear-gradient(135deg, #ffc107, #e0a800);
            color: #fff;
        }
        .action-icons a {
            margin: 0 8px;
            font-size: 1.4rem;
            color: #2d3436;
            transition: transform 0.3s ease, color 0.3s ease;
        }
        .action-icons a:hover {
            transform: scale(1.2);
        }
        .action-icons .fa-eye:hover {
            color: #4e73df;
        }
        .action-icons .fa-edit:hover {
            color: #28a745;
        }
        .action-icons .fa-trash:hover {
            color: #e74c3c;
        }
        .btn-add {
            background: linear-gradient(135deg, #4e73df, #224abe);
            color: #fff;
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            margin-bottom: 15px;
            transition: background 0.3s ease;
        }
        .btn-add:hover {
            background: linear-gradient(135deg, #224abe, #1d3f9f);
        }
    </style>
</head>
<body>

<div class="container">
    <h2><i class="fa-solid fa-truck"></i> Transport Management</h2>
    <p>Manage transport details for invoices, including delivery methods, company details, and tracking status.</p>


    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Invoice ID</th>
                    <th>From</th>
                    <th>Destination</th>
                    <th>Transport Through</th>
                    <th>Company Name</th>
                    <th>Status</th>
                    
                </tr>
            </thead>
            <tbody>
                <?php
                $query = "SELECT * FROM transpositions";
                $result = $conn->query($query);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $statusClass = ($row['status'] == 'Delivered') ? 'delivered' : 'pending';
                        echo "<tr>
                                <td>{$row['invoice_id']}</td>
                                <td>{$row['from_location']}</td>
                                <td>{$row['destination']}</td>
                                <td>{$row['transport_through']}</td>
                                <td>{$row['transport_company']}</td>
                                <td><span class='badge {$statusClass}'>{$row['status']}</span></td>
                                
                            </tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>No transport orders found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
