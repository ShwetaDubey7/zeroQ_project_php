<?php
// actions/handle_invitation.php
session_start();
require_once '../includes/db.php';

// Security check: Sirf logged-in admin hi invite bhej sakta hai
if (!isset($_SESSION['business_id'])) {
    header('Location: ../admin/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $businessId = $_SESSION['business_id'];
    $userEmail = trim($_POST['user_email']);

    if (empty($userEmail)) {
        $_SESSION['invite_error'] = 'User email cannot be empty.';
        header('Location: ../admin/dashboard.php');
        exit;
    }

    try {
        // Step 1: User will be find by emailid
        $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
        mysqli_stmt_bind_param($stmt, "s", $userEmail);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        if (!$user) {
            $_SESSION['invite_error'] = 'User is not registered by this email.';
            header('Location: ../admin/dashboard.php');
            exit;
        }
        $userId = $user['id'];

        // Step 2: insert new records into 'invitations' table
        $stmt = mysqli_prepare($conn, "INSERT INTO invitations (business_id, user_id) VALUES (?, ?)");
        mysqli_stmt_bind_param($stmt, "ii", $businessId, $userId);
        
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['invite_success'] = 'Invitation sent successfully!';
        } else {
            $_SESSION['invite_error'] = 'Error occurred while sending invitation. It may already be pending.';
        }
        mysqli_stmt_close($stmt);

    } catch (mysqli_sql_exception $e) {
        error_log("Database error in handle_invitation.php: " . $e->getMessage());
        $_SESSION['invite_error'] = 'Database error occurred.';
    } finally {
        mysqli_close($conn);
    }

    header('Location: ../admin/dashboard.php');
    exit;
}
?>
