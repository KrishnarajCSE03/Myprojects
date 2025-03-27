<?php
$errorMessage = "";
if (isset($_GET['error'])) {
    if ($_GET['error'] == "user_not_found") {
        $errorMessage = "No account found with this email!";
    } elseif ($_GET['error'] == "invalid_password") {
        $errorMessage = "Incorrect password! Please try again.";
    } elseif ($_GET['error'] == "session_expired") {
        $errorMessage = "Your session expired. Please log in again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Billing System</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>

    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="login-card shadow-lg p-4">
            <h3 class="text-center text-primary fw-bold mb-4">Welcome Back</h3>
            <p class="text-center text-muted">Sign in to continue</p>

            <!-- Error Message Display -->
            <?php if (!empty($errorMessage)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fa fa-exclamation-circle"></i> <?php echo $errorMessage; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <form id="login-form" action="authenticate.php" method="POST">
                <div class="mb-3">
                    <label class="form-label"><i class="fa fa-envelope"></i> Email</label>
                    <input type="email" name="email" id="email" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label"><i class="fa fa-lock"></i> Password</label>
                    <input type="password" name="password" id="password" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    <i class="fa fa-sign-in-alt"></i> Login
                </button>
            </form>

            <div class="text-center mt-3">
                <a href="forgot_password.php" class="text-danger"><i class="fa fa-key"></i> Forgot Password?</a>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
