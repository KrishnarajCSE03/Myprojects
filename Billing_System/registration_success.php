<?php
if (!isset($_GET['name'])) {
    header("Location: register.php");
    exit();
}
$name = htmlspecialchars($_GET['name']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Successful</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- Custom Styles -->
    <link rel="stylesheet" href="assets/styles.css">
    
    <style>
        body {
            background: linear-gradient(to right, #1e3c72, #2a5298);
            font-family: 'Poppins', sans-serif;
            text-align: center;
            color: white;
        }
        .success-container {
            margin-top: 100px;
        }
        .success-card {
            background: white;
            border-radius: 12px;
            padding: 40px;
            width: 400px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            text-align: center;
            color: black;
            animation: fadeIn 0.8s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .btn {
            font-size: 18px;
            padding: 12px;
            border-radius: 8px;
            transition: all 0.3s ease-in-out;
        }
        .btn:hover {
            transform: scale(1.05);
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.3);
        }
    </style>

    <!-- Redirect to login page after 5 seconds -->
    <script>
        setTimeout(() => {
            window.location.href = "login.php";
        }, 5000);
    </script>
</head>
<body>

    <div class="container d-flex justify-content-center align-items-center min-vh-100 success-container">
        <div class="success-card">
            <i class="fa fa-check-circle text-success" style="font-size: 60px;"></i>
            <h2 class="fw-bold mt-3">Account Created!</h2>
            <p>Welcome, <b><?php echo $name; ?></b>. Your account has been successfully created.</p>
            <p>Redirecting you to the login page...</p>
            <a href="login.php" class="btn btn-primary"><i class="fa fa-sign-in-alt"></i> Go to Login</a>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
