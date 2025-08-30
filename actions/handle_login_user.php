<?php
session_start();
require_once '../includes/db.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $_SESSION['error'] = 'Email and password are required.';
        header('Location: ../user/login_user.php');
        exit;
    }

    try {
        // Find the user by email
        $stmt = mysqli_prepare($conn, "SELECT id, name, password_hash FROM users WHERE email = ?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        // Verify password
        if ($user && password_verify($password, $user['password_hash'])) {
            // Create Session
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            
            header('Location: ../user/dashboard.php');
            exit;
        } else {
            $_SESSION['error'] = 'Invalid email or password.';
            header('Location: ../user/login_user.php');
            exit;
        }
    } catch (mysqli_sql_exception $e) {
        $_SESSION['error'] = 'A database error occurred.';
        header('Location: ../user/login_user.php');
    } finally {
        mysqli_close($conn);
    }
    exit;
} else {
    header('Location: ../user/login_user.php');
    exit;
}
?>
