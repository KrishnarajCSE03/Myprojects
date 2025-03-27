<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Billing System</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>

    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="card shadow-lg p-4" style="width: 400px; border-radius: 15px;">
            <h3 class="text-center text-warning mb-3">🔑 Forgot Password?</h3>

            <form action="reset_process.php" method="POST">
                <div class="mb-3">
                    <label class="form-label"><i class="bi bi-envelope"></i> Enter Your Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-warning w-100">
                    <i class="bi bi-send"></i> Send Reset Link
                </button>
            </form>

            <div class="text-center mt-3">
                <a href="login.php" class="text-primary"><i class="bi bi-arrow-left"></i> Back to Login</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
