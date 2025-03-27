<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!-- ✅ Modern Sidebar Styles -->
<style>
/* 🌙 Sidebar Container */
.sidebar {
    position: fixed;
    top: 0;
    left: 0;
    width: 250px;
    height: 100vh;
    background: #1E1E2F;
    padding: 20px;
    display: flex;
    flex-direction: column;
    box-shadow: 3px 0 15px rgba(0, 0, 0, 0.2);
    z-index: 1000;
    transition: all 0.3s ease;
    overflow-y: auto;
}

/* 🌟 Sidebar Branding */
.sidebar h3 {
    color: #fff;
    font-weight: 700;
    margin-bottom: 30px;
    text-align: center;
    font-size: 1.8rem;
    letter-spacing: 2px;
    text-transform: uppercase;
    background: linear-gradient(90deg, #4ECDC4, #556270);
    -webkit-background-clip: text;
    color: transparent;
}

/* 📂 Sidebar List */
.sidebar ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

/* 🔗 Sidebar Links */
.sidebar ul li a {
    display: flex;
    align-items: center;
    padding: 15px 20px;
    color: #d1d1e9;
    text-decoration: none;
    font-size: 1rem;
    font-weight: 500;
    border-radius: 8px;
    margin-bottom: 10px;
    transition: 0.3s ease;
    position: relative;
    overflow: hidden;
}

/* 🖼️ Icon Styling */
.sidebar ul li a i {
    margin-right: 15px;
    font-size: 1.2rem;
    transition: 0.3s ease;
}

/* 🌈 Hover & Active Effects */
.sidebar ul li a:hover,
.sidebar ul li a.active {
    background: linear-gradient(90deg, #4ECDC4, #556270);
    color: #fff;
    border-radius: 4px;
    transform: translateX(5px);
    box-shadow: 3px 3px 10px rgba(0, 0, 0, 0.3);
}

/* 🚪 Special Styling for Logout */
.sidebar ul li a.text-danger {
    background: #e74c3c;
    color: #fff;
}

.sidebar ul li a.text-danger:hover {
    background: #c0392b;
    transform: translateX(5px);
}

/* 📱 Responsive Sidebar */
@media (max-width: 768px) {
    .sidebar {
        width: 200px;
    }

    .sidebar h3 {
        font-size: 1.4rem;
    }

    .sidebar ul li a {
        padding: 12px;
        font-size: 0.9rem;
    }

    .sidebar ul li a i {
        font-size: 1rem;
    }
}

/* 🖥️ Adjust Main Content for Sidebar */


@media (max-width: 768px) {
    .main-content {
        margin-left: 200px;
    }
}
</style>

<!-- ✅ Sidebar Structure -->
<div class="sidebar">
    <h3>Billing System</h3>
    <ul>
        <li>
            <a href="dashboard.php" class="<?= strpos($_SERVER['PHP_SELF'], 'dashboard.php') !== false ? 'active' : ''; ?>">
                <i class="fa fa-tachometer-alt"></i> Dashboard
            </a>
        </li>
        <li>
            <a href="invoices.php" class="<?= strpos($_SERVER['PHP_SELF'], 'invoices.php') !== false ? 'active' : ''; ?>">
                <i class="fa fa-file-invoice"></i> Invoices
            </a>
        </li>
        <li>
            <a href="customers.php" class="<?= strpos($_SERVER['PHP_SELF'], 'customers.php') !== false ? 'active' : ''; ?>">
                <i class="fa fa-users"></i> Customers
            </a>
        </li>
        <li>
            <a href="products.php" class="<?= strpos($_SERVER['PHP_SELF'], 'products.php') !== false ? 'active' : ''; ?>">
                <i class="bi bi-box-seam"></i> Products
            </a>
        </li>
        <li>
            <a href="transportation.php" class="<?= strpos($_SERVER['PHP_SELF'], 'transportation.php') !== false ? 'active' : ''; ?>">
                <i class="fa-solid fa-truck"></i> Transportation
            </a>
        </li>
        <li>
            <a href="accounts.php" class="<?= strpos($_SERVER['PHP_SELF'], 'accounts.php') !== false ? 'active' : ''; ?>">
                <i class="fa fa-clipboard-list"></i> Accounts
            </a>
        </li>
        <li>
            <a href="logout.php" class="text-danger">
                <i class="fa fa-sign-out-alt"></i> Logout
            </a>
        </li>
    </ul>
</div>

<!-- ✅ Include Font Awesome for Icons -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
