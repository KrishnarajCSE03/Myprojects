<?php
if (!isset($_GET['link'])) {
    header("Location: forgot-password.php");
    exit();
}
$reset_link = htmlspecialchars($_GET['link']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset Link</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="card p-4 text-center shadow-lg" style="max-width: 400px;">
            <h3 class="text-primary">🔑 Reset Link</h3>
            <p>Copy this link and paste it in your browser:</p>
            <input type="text" class="form-control" value="<?php echo $reset_link; ?>" readonly>
            <a href="<?php echo $reset_link; ?>" class="btn btn-primary mt-3" target="_blank">Go to Reset Page</a>
            <br><br>
            <a href="login.php" class="btn btn-secondary">Back to Login</a>
        </div>
    </div>

</body>
</html>
