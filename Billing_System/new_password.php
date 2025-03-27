<?php
session_start();
include 'config/db.php';

if (!isset($_GET['token'])) {
    echo "<script>alert('Invalid or expired reset link!'); window.location.href='forgot_password.php';</script>";
    exit();
}

$token = $_GET['token'];

// Verify token
$stmt = $conn->prepare("SELECT user_id FROM users WHERE reset_token=? AND reset_token_expiry > NOW()");
$stmt->bind_param("s", $token);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows == 0) {
    echo "<script>alert('Invalid or expired reset link!'); window.location.href='forgot_password.php';</script>";
    exit();
}

$stmt->bind_result($user_id);
$stmt->fetch();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- Custom Styles -->
    <link rel="stylesheet" href="assets/styles.css">
    
    <style>
        /* Background with gradient */
        body {
            background: linear-gradient(to right, #1e3c72, #2a5298);
            font-family: 'Poppins', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            animation: fadeIn 0.5s ease-in-out;
        }

        /* Fade-in animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Card styling */
        .reset-card {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            width: 400px;
            text-align: center;
        }

        /* Form input styles */
        .form-control {
            border-radius: 8px;
            padding: 12px;
            border: 2px solid #ccc;
            transition: all 0.3s ease-in-out;
        }

        /* Focus effect on input */
        .form-control:focus {
            border: 2px solid #2a5298;
            box-shadow: 0px 0px 10px rgba(42, 82, 152, 0.5);
            outline: none;
            transform: scale(1.02);
        }

        /* Show/Hide password toggle */
        .password-wrapper {
            position: relative;
        }

        .toggle-password {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            cursor: pointer;
            color: gray;
        }

        /* Button hover effect */
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

        /* Back to Login link */
        a {
            text-decoration: none;
            transition: all 0.3s ease-in-out;
        }

        a:hover {
            color: #2a5298;
            font-weight: bold;
            transform: scale(1.02);
        }
    </style>
</head>
<body>

    <div class="reset-card">
        <i class="fa fa-lock text-primary" style="font-size: 50px;"></i>
        <h2 class="fw-bold mt-3">Reset Password</h2>
        <p>Enter a new password below.</p>

        <form action="update_password.php" method="POST">
            <input type="hidden" name="token" value="<?php echo $token; ?>">

            <div class="mb-3 password-wrapper">
                <label class="form-label">New Password</label>
                <input type="password" id="password" name="password" class="form-control" required>
                <i class="fa fa-eye toggle-password" onclick="togglePassword()"></i>
            </div>

            <button type="submit" class="btn btn-success w-100"><i class="fa fa-key"></i> Reset Password</button>
        </form>

        <div class="mt-3">
            <a href="login.php" class="text-primary"><i class="fa fa-arrow-left"></i> Back to Login</a>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Show/Hide Password Toggle
        function togglePassword() {
            var passwordInput = document.getElementById("password");
            var icon = document.querySelector(".toggle-password");
            
            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            } else {
                passwordInput.type = "password";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            }
        }
    </script>

</body>
</html>
