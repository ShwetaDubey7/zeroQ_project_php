<?php
session_start();

require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $_SESSION['error'] = 'Email and password are required.';
        header('Location: ../admin/login.php');
        exit; 
    }

    try {
        $stmt = mysqli_prepare($conn, "SELECT id, business_name, password_hash FROM businesses WHERE email = ?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $business = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        if ($business && password_verify($password, $business['password_hash'])) {
            
            session_regenerate_id(true);
            $_SESSION['business_id'] = $business['id'];
            $_SESSION['business_name'] = $business['business_name'];
            
            // THEEK KIYA GAYA REDIRECT PATH
            header('Location: ../admin/dashboard.php');
            exit;

        } else {
            $_SESSION['error'] = 'Invalid email or password.';
            header('Location: ../admin/login.php');
            exit;
        }

    } catch (mysqli_sql_exception $e) {
        error_log("Database error in handle_login.php: " . $e->getMessage());
        $_SESSION['error'] = 'A database error occurred.';
        header('Location: ../admin/login.php');
        exit;
    } finally {
        if (isset($conn) && $conn) {
            mysqli_close($conn);
        }
    }

} else {
    header('Location: ../admin/login.php');
    exit;
}

