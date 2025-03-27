<?php
session_start();
include 'config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);

    // Check if email exists
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 0) {
        header("Location: forgot-password.php?error=no_account");
        exit();
    }

    // Generate reset token
    $token = bin2hex(random_bytes(32)); // Secure random token
    $expiry = date("Y-m-d H:i:s", strtotime("+1 hour")); // Token valid for 1 hour

    // Store token in database
    $update = $conn->prepare("UPDATE users SET reset_token=?, reset_token_expiry=? WHERE email=?");
    $update->bind_param("sss", $token, $expiry, $email);
    
    if ($update->execute()) {
        // Generate reset link
        $reset_link = "http://localhost:8080/billing_system/new_password.php?token=" . $token;
        
        // For local testing, display reset link instead of email
        header("Location: reset_link.php?link=" . urlencode($reset_link));
        exit();
    } else {
        header("Location: forgot_password.php?error=database_error");
        exit();
    }
}
?>
