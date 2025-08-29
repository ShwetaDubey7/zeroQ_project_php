<?php
session_start();
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Basic Validation
    if (empty($name) || empty($email) || empty($password)) {
        $_SESSION['error'] = 'All fields are required.';
        header('Location: ../user/register_user.php');
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = 'Invalid email format.';
        header('Location: ../user/register_user.php');
        exit;
    }

    try {
        // Check if email already exists
        $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if (mysqli_num_rows($result) > 0) {
            $_SESSION['error'] = 'Email address is already registered.';
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            header('Location: ../user/register_user.php');
            exit;
        }
        mysqli_stmt_close($stmt);

        // Hash the password
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        // Insert new user
        $stmt = mysqli_prepare($conn, "INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "sss", $name, $email, $passwordHash);
        
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success'] = 'Registration successful! Please log in.';
            header('Location: ../user/login_user.php');
        } else {
            $_SESSION['error'] = 'An error occurred. Please try again.';
            header('Location: ../user/register_user.php');
        }
        mysqli_stmt_close($stmt);

    } catch (mysqli_sql_exception $e) {
        $_SESSION['error'] = 'A database error occurred.';
        header('Location: ../user/register_user.php');
    } finally {
        mysqli_close($conn);
    }
    exit;
} else {
    header('Location: ../user/register_user.php');
    exit;
}
?>