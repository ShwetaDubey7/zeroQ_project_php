<?php
// api.php
// This is the central API endpoint for all AJAX requests from the frontend.

// Set the content type to JSON so browsers and scripts know how to handle the response.
header('Content-Type: application/json');
session_start();

require_once 'includes/db.php';

// Initialize a default response array.
$response = ['success' => false, 'message' => 'Invalid action.'];

// Check for a specific action requested via a GET parameter.
if (isset($_GET['action'])) {
    $action = $_GET['action'];

    try {
        // --- ACTION: get_status (for customer status page) ---
        if ($action === 'get_status' && isset($_SESSION['queue_id'])) {
            $queueId = $_SESSION['queue_id'];
            
            // Fetch customer's own data
            $stmt = mysqli_prepare($conn, "SELECT * FROM queues WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "i", $queueId);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $customerData = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);

            if ($customerData) {
                // Fetch associated business data
                $businessId = $customerData['business_id'];
                $stmt = mysqli_prepare($conn, "SELECT business_name, queue_status FROM businesses WHERE id = ?");
                mysqli_stmt_bind_param($stmt, "i", $businessId);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $businessData = mysqli_fetch_assoc($result);
                mysqli_stmt_close($stmt);

                // Calculate people ahead in the queue
                $peopleAhead = 0;
                if ($customerData['status'] === 'waiting') {
                    $stmt = mysqli_prepare($conn, "SELECT COUNT(id) as count FROM queues WHERE business_id = ? AND status = 'waiting' AND join_time < ?");
                    mysqli_stmt_bind_param($stmt, "is", $customerData['business_id'], $customerData['join_time']);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);
                    $aheadData = mysqli_fetch_assoc($result);
                    $peopleAhead = $aheadData['count'];
                    mysqli_stmt_close($stmt);
                }

                // Prepare a successful response with all the needed data.
                $response = [
                    'success' => true,
                    'customer' => $customerData,
                    'business' => $businessData,
                    'peopleAhead' => $peopleAhead
                ];
            } else {
                $response['message'] = 'Queue ticket not found.';
            }
        }
        // --- ACTION: get_queue (for admin dashboard) ---
        elseif ($action === 'get_queue' && isset($_SESSION['business_id'])) {
            $businessId = $_SESSION['business_id'];

            // Fetch business data (specifically queue status)
            $stmt = mysqli_prepare($conn, "SELECT queue_status FROM businesses WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "i", $businessId);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $business = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);

            // Fetch the list of all customers currently in the queue
            $stmt = mysqli_prepare($conn, "SELECT * FROM queues WHERE business_id = ? AND status IN ('waiting', 'serving') ORDER BY join_time ASC");
            mysqli_stmt_bind_param($stmt, "i", $businessId);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $customerList = mysqli_fetch_all($result, MYSQLI_ASSOC);
            mysqli_stmt_close($stmt);

            // Prepare a successful response
            $response = [
                'success' => true,
                'queueStatus' => $business['queue_status'],
                'customerList' => $customerList,
                'waitingCount' => count($customerList)
            ];
        }

    } catch (mysqli_sql_exception $e) {
        // In case of a database error, send back a generic error message.
        $response = ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
    }
}

// Finally, encode the response array into a JSON string and output it.
echo json_encode($response);
exit;
