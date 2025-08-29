<?php
session_start();
require_once '../includes/db.php'; // Provides $conn

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $businessName = trim($_POST['business_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Basic Validation
    if (empty($businessName) || empty($email) || empty($password)) {
        $_SESSION['error'] = 'All fields are required.';
        header('Location: register.php');
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = 'Invalid email format.';
        header('Location: register.php');
        exit;
    }

    try {
        // Check if email already exists
        $stmt = mysqli_prepare($conn, "SELECT id FROM businesses WHERE email = ?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if (mysqli_num_rows($result) > 0) {
            $_SESSION['error'] = 'Email address is already registered.';
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            header('Location: register.php');
            exit;
        }
        mysqli_stmt_close($stmt);

        // Generate Unique Queue Code
        do {
            $queueCode = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 6);
            $stmt = mysqli_prepare($conn, "SELECT id FROM businesses WHERE queue_code = ?");
            mysqli_stmt_bind_param($stmt, "s", $queueCode);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $codeExists = mysqli_num_rows($result) > 0;
            mysqli_stmt_close($stmt);
        } while ($codeExists);
        
        // Hash the password
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        // Insert new business
        $stmt = mysqli_prepare($conn, "INSERT INTO businesses (business_name, email, password_hash, queue_code) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "ssss", $businessName, $email, $passwordHash, $queueCode);
        
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success'] = 'Registration successful! Please log in.';
            header('Location: login.php');
        } else {
            $_SESSION['error'] = 'An error occurred during registration. Please try again.';
            header('Location: register.php');
        }
        mysqli_stmt_close($stmt);

    } catch (mysqli_sql_exception $e) {
        $_SESSION['error'] = 'A database error occurred.';
        header('Location: register.php');
    } finally {
        mysqli_close($conn);
    }
    exit;
} else {
    header('Location: register.php');
    exit;
}
?>