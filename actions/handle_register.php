<?php
session_start();
// Correct path to db.php
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // If the form was not submitted, redirect to the register page
    header('Location: ../admin/register.php');
    exit;
}

// Get data from the form
$businessName = trim($_POST['business_name']);
$email = trim($_POST['email']);
$password = $_POST['password'];

// Basic validation
if (empty($businessName) || empty($email) || empty($password)) {
    $_SESSION['error'] = 'All fields are required.';
    header('Location: ../admin/register.php');
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error'] = 'Please enter a valid email address.';
    header('Location: ../admin/register.php');
    exit;
}

try {
    // Check if the email already exists in the businesses table
    $stmt = mysqli_prepare($conn, "SELECT id FROM businesses WHERE email = ?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) > 0) {
        $_SESSION['error'] = 'This email is already registered as a business.';
        mysqli_stmt_close($stmt);
        header('Location: ../admin/register.php');
        exit;
    }
    mysqli_stmt_close($stmt);

    // Hash the password
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    
    // Create a unique 6-digit queue code
    $queueCode = strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));

    // Insert the new business into the database
    $stmt = mysqli_prepare($conn, "INSERT INTO businesses (business_name, email, password_hash, queue_code) VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "ssss", $businessName, $email, $passwordHash, $queueCode);
    
    if (mysqli_stmt_execute($stmt)) {
        // Success! Set the success message and redirect to the login page
        $_SESSION['success'] = 'Registration successful! Please log in.';
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        header('Location: ../admin/login.php');
        exit;
    } else {
        throw new Exception('Could not execute statement.');
    }

} catch (Exception $e) {
    error_log("Registration Error: " . $e->getMessage());
    $_SESSION['error'] = 'An error occurred during registration. Please try again.';
    if (isset($stmt)) mysqli_stmt_close($stmt);
    if ($conn) mysqli_close($conn);
    header('Location: ../admin/register.php');
    exit;
}
?>
