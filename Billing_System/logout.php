<?php
session_start();
include 'config/db.php';

// Function to get user IP address
function getUserIP() {
    return $_SERVER['REMOTE_ADDR'];
}

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $ip_address = getUserIP();

    // Check if the user exists before logging
    $checkUser = $conn->prepare("SELECT user_id FROM users WHERE user_id = ?");
    $checkUser->bind_param("i", $user_id);
    $checkUser->execute();
    $checkUser->store_result();

    if ($checkUser->num_rows > 0) {
        // Log user logout if the user exists
        $stmt = $conn->prepare("INSERT INTO activity_logs (user_id, action, ip_address) VALUES (?, 'User logged out', ?)");
        $stmt->bind_param("is", $user_id, $ip_address);
        $stmt->execute();
    }

    $checkUser->close();
}

// Destroy session and redirect
session_destroy();
header("Location: login.php?success=logged_out");
exit();
?>
