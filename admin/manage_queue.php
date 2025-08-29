<?php
// admin/manage_queue.php

session_start();

// Security: Ensure user is logged in
if (!isset($_SESSION['business_id'])) {
    header('Location: login.php');
    exit;
}

// Check if an action was posted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    
    require_once '../includes/db.php'; // This file must provide a $conn variable for MySQLi

    $businessId = $_SESSION['business_id'];
    $action = $_POST['action'];

    try {
        switch ($action) {
            case 'call_next':
                // --- Updated Logic for Calling Next Customer ---

                // Start a transaction to ensure both updates happen or neither do.
                mysqli_begin_transaction($conn);

                // Step 1: Find any customer currently being served and mark them as 'completed'.
                $stmt1 = mysqli_prepare($conn, "UPDATE queues SET status = 'completed' WHERE business_id = ? AND status = 'serving'");
                mysqli_stmt_bind_param($stmt1, "i", $businessId);
                mysqli_stmt_execute($stmt1);
                mysqli_stmt_close($stmt1);

                // Step 2: Find the next customer in the 'waiting' line (the one who joined earliest).
                $stmt2 = mysqli_prepare($conn, "SELECT id FROM queues WHERE business_id = ? AND status = 'waiting' ORDER BY join_time ASC LIMIT 1");
                mysqli_stmt_bind_param($stmt2, "i", $businessId);
                mysqli_stmt_execute($stmt2);
                $result = mysqli_stmt_get_result($stmt2);
                $nextCustomer = mysqli_fetch_assoc($result);
                mysqli_stmt_close($stmt2);

                if ($nextCustomer) {
                    // Step 3: If a waiting customer was found, update their status to 'serving'.
                    $stmt3 = mysqli_prepare($conn, "UPDATE queues SET status = 'serving' WHERE id = ?");
                    mysqli_stmt_bind_param($stmt3, "i", $nextCustomer['id']);
                    mysqli_stmt_execute($stmt3);
                    mysqli_stmt_close($stmt3);
                }

                // Commit the transaction to save the changes.
                mysqli_commit($conn);
                break;

            case 'open_queue':
                // Update the business's status to 'open'
                $stmt = mysqli_prepare($conn, "UPDATE businesses SET queue_status = 'open' WHERE id = ?");
                mysqli_stmt_bind_param($stmt, "i", $businessId);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
                break;

            case 'close_queue':
                // Update the business's status to 'closed'
                $stmt = mysqli_prepare($conn, "UPDATE businesses SET queue_status = 'closed' WHERE id = ?");
                mysqli_stmt_bind_param($stmt, "i", $businessId);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
                break;
        }
    } catch (mysqli_sql_exception $e) {
        // If any error occurs, roll back the transaction
        mysqli_rollback($conn);
        error_log("Database error in manage_queue.php: " . $e->getMessage());
        $_SESSION['error'] = 'An error occurred while managing the queue.';
    } finally {
        // Always close the connection
        mysqli_close($conn);
    }
}

// After the action is complete, redirect back to the dashboard.
header('Location: dashboard.php');
exit;
?>
