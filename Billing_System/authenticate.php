<?php
session_start();
include 'config/db.php';

// Function to get user IP address
function getUserIP() {
    return $_SERVER['REMOTE_ADDR'];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $ip_address = getUserIP();

    // Check if email exists
    $stmt = $conn->prepare("SELECT user_id, password_hash, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($user_id, $password_hash, $role);
        $stmt->fetch();

        // Verify password
        if (password_verify($password, $password_hash)) {
            $_SESSION['user_id'] = $user_id;
            $_SESSION['role'] = $role;

            // Log successful login
            $log_stmt = $conn->prepare("INSERT INTO activity_logs (user_id, action, ip_address) VALUES (?, 'User logged in', ?)");
            $log_stmt->bind_param("is", $user_id, $ip_address);
            $log_stmt->execute();

            // Redirect based on user role
            if ($role == "admin") {
                header("Location: dashboard.php");
            } else {
                header("Location: dashboard.php");
            }
            exit();
        } else {
            // Log failed login attempt (wrong password)
            $log_stmt = $conn->prepare("INSERT INTO activity_logs (user_id, action, ip_address) VALUES (NULL, 'Failed login attempt (wrong password)', ?)");
            $log_stmt->bind_param("s", $ip_address);
            $log_stmt->execute();

            header("Location: login.php?error=invalid_password");
            exit();
        }
    } else {
        // Log failed login (invalid email)
        $log_stmt = $conn->prepare("INSERT INTO activity_logs (user_id, action, ip_address) VALUES (NULL, 'Failed login attempt (email not found)', ?)");
        $log_stmt->bind_param("s", $ip_address);
        $log_stmt->execute();

        header("Location: login.php?error=user_not_found");
        exit();
    }
}
?>
