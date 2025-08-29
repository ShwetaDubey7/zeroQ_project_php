<?php
// handle_join.php

// This script handles the final step of joining the queue.
// It receives the customer's name and the business ID from join.php.

session_start();

require_once 'includes/db.php';
require_once 'includes/Queue.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['customer_name'], $_POST['business_id'])) {
    
    $customerName = trim($_POST['customer_name']);
    $businessId = (int)$_POST['business_id'];

    if (empty($customerName) || empty($businessId)) {
        // Should not happen with 'required' attribute, but good to have.
        header('Location: index.php?error=Invalid data.');
        exit;
    }
    
    $queue = new Queue($conn);

    try {
        // Use the join method from our Queue class.
        $newQueueId = $queue->join($businessId, $customerName);

        if ($newQueueId) {
            // Success! Store the queue ID in the session to identify the user.
            $_SESSION['queue_id'] = $newQueueId;
            
            // Redirect the customer to their personal status page.
            header('Location: status.php');
            exit;
        } else {
            // Handle case where joining failed for some reason.
            header('Location: index.php?error=Could not join queue. Please try again.');
            exit;
        }

    } catch (mysqli_sql_exception $e) {
        // Handle database errors.
        error_log("Database error in handle_join.php: " . $e->getMessage());
        header('Location: index.php?error=A database error occurred.');
        exit;
    }

} else {
    // Redirect if accessed directly.
    header('Location: index.php');
    exit;
}
