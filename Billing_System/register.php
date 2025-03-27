<?php
// Check for errors from register_process.php
$error = "";
$success = "";

if (isset($_GET['error'])) {
    if ($_GET['error'] == "email_exists") {
        $error = "This email is already registered. Try a different one!";
    } elseif ($_GET['error'] == "registration_failed") {
        $error = "Something went wrong! Please try again.";
    }
}

if (isset($_GET['success']) && $_GET['success'] == "registered") {
    $success = "Registration successful! You can now log in.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Billing System</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>

    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="login-card shadow-lg p-4">
            <h3 class="text-center text-success fw-bold mb-4">Create an Account</h3>

            <!-- Display Error Message -->
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fa fa-exclamation-circle"></i> <?php echo $error; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Display Success Message -->
            <?php if (!empty($success)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fa fa-check-circle"></i> <?php echo $success; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <form action="register_process.php" method="POST">
                <div class="mb-3">
                    <label class="form-label"><i class="fa fa-user"></i> Full Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label"><i class="fa fa-envelope"></i> Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label"><i class="fa fa-lock"></i> Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <!-- Role Selection Dropdown -->
                <div class="mb-3">
                    <label class="form-label"><i class="fa fa-user-tag"></i> Select Role</label>
                    <select name="role" class="form-control" required>
                        <option value="biller">Biller</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-success w-100">
                    <i class="fa fa-check-circle"></i> Register
                </button>
            </form>

            <div class="text-center mt-3">
                <a href="login.php" class="text-primary"><i class="fa fa-arrow-left"></i> Back to Login</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
