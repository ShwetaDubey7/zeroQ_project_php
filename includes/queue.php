<?php
// includes/Queue.php

/**
 * The Queue class handles all business logic related to queue management.
 * It interacts with the database to perform CRUD (Create, Read, Update, Delete) operations.
 * This is an example of Object-Oriented Programming (OOP) in PHP.
 */
class Queue
{
    /**
     * The MySQLi database connection object.
     * @var mysqli
     */
    private $conn;

    /**
     * The constructor is called when a new Queue object is created.
     * It requires a MySQLi database connection object to be passed in.
     * This is an example of Dependency Injection, a key OOP concept.
     *
     * @param mysqli $conn The database connection object.
     */
    public function __construct(mysqli $conn)
    {
        $this->conn = $conn;
    }

    /**
     * Finds a business by its unique queue code.
     * Used to verify if a customer can join a specific queue.
     *
     * @param string $queueCode The unique code for the business's queue.
     * @return array|null The business data as an associative array, or null if not found.
     */
    public function getBusinessByQueueCode(string $queueCode)
    {
        $stmt = mysqli_prepare($this->conn, "SELECT id, business_name, queue_status FROM businesses WHERE queue_code = ?");
        if (!$stmt) {
            throw new mysqli_sql_exception("Failed to prepare statement: " . mysqli_error($this->conn));
        }
        mysqli_stmt_bind_param($stmt, "s", $queueCode);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $business = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        return $business;
    }

    /**
     * Adds a new customer to the queue for a specific business.
     *
     * @param int $businessId The ID of the business the customer is joining.
     * @param string $customerName The name of the customer.
     * @return int|false The ID of the newly inserted queue entry, or false on failure.
     */
    public function join(int $businessId, string $customerName)
    {
        // Get the next available token number for this business
        $stmt = mysqli_prepare($this->conn, "SELECT MAX(token_number) AS max_token FROM queues WHERE business_id = ?");
        if (!$stmt) {
            throw new mysqli_sql_exception("Failed to prepare statement: " . mysqli_error($this->conn));
        }
        mysqli_stmt_bind_param($stmt, "i", $businessId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        $next_token_number = ($row['max_token'] ?? 0) + 1;

        // Insert the new customer into the queue
        $stmt = mysqli_prepare($this->conn, "INSERT INTO queues (business_id, customer_name, token_number, status) VALUES (?, ?, ?, 'waiting')");
        if (!$stmt) {
            throw new mysqli_sql_exception("Failed to prepare statement: " . mysqli_error($this->conn));
        }
        mysqli_stmt_bind_param($stmt, "isi", $businessId, $customerName, $next_token_number);
        
        if (mysqli_stmt_execute($stmt)) {
            $insert_id = mysqli_insert_id($this->conn);
            mysqli_stmt_close($stmt);
            return $insert_id;
        } else {
            mysqli_stmt_close($stmt);
            return false;
        }
    }

    /**
     * Retrieves the current queue for a given business.
     *
     * @param int $businessId The ID of the business.
     * @return array An array of queue entries.
     */
    public function getQueueForBusiness(int $businessId)
    {
        $stmt = mysqli_prepare($this->conn, "SELECT id, customer_name, token_number, status, join_time, called_time FROM queues WHERE business_id = ? AND status IN ('waiting', 'serving') ORDER BY join_time ASC");
        if (!$stmt) {
            throw new mysqli_sql_exception("Failed to prepare statement: " . mysqli_error($this->conn));
        }
        mysqli_stmt_bind_param($stmt, "i", $businessId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $queue = mysqli_fetch_all($result, MYSQLI_ASSOC);
        mysqli_stmt_close($stmt);
        return $queue;
    }
    
    /**
     * Calls the next customer in the queue for a given business.
     *
     * @param int $businessId The ID of the business.
     * @return bool True on success, false on failure (e.g., no waiting customers).
     */
    public function callNextCustomer(int $businessId)
    {
        // Find the oldest waiting customer
        $stmt = mysqli_prepare($this->conn, "SELECT id FROM queues WHERE business_id = ? AND status = 'waiting' ORDER BY join_time ASC LIMIT 1");
        if (!$stmt) {
            throw new mysqli_sql_exception("Failed to prepare statement: " . mysqli_error($this->conn));
        }
        mysqli_stmt_bind_param($stmt, "i", $businessId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $nextCustomer = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        if ($nextCustomer) {
            // Update their status to 'serving' and set called_time
            $stmt = mysqli_prepare($this->conn, "UPDATE queues SET status = 'serving', called_time = NOW() WHERE id = ?");
            if (!$stmt) {
                throw new mysqli_sql_exception("Failed to prepare statement: " . mysqli_error($this->conn));
            }
            mysqli_stmt_bind_param($stmt, "i", $nextCustomer['id']);
            $success = mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            return $success;
        }
        return false; // No waiting customers
    }

    /**
     * Marks a customer as completed.
     *
     * @param int $queueEntryId The ID of the queue entry.
     * @return bool True on success, false on failure.
     */
    public function completeCustomer(int $queueEntryId)
    {
        $stmt = mysqli_prepare($this->conn, "UPDATE queues SET status = 'completed', completed_time = NOW() WHERE id = ?");
        if (!$stmt) {
            throw new mysqli_sql_exception("Failed to prepare statement: " . mysqli_error($this->conn));
        }
        mysqli_stmt_bind_param($stmt, "i", $queueEntryId);
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $success;
    }

    /**
     * Marks a customer as cancelled.
     *
     * @param int $queueEntryId The ID of the queue entry.
     * @return bool True on success, false on failure.
     */
    public function cancelCustomer(int $queueEntryId)
    {
        $stmt = mysqli_prepare($this->conn, "UPDATE queues SET status = 'cancelled' WHERE id = ?");
        if (!$stmt) {
            throw new mysqli_sql_exception("Failed to prepare statement: " . mysqli_error($this->conn));
        }
        mysqli_stmt_bind_param($stmt, "i", $queueEntryId);
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $success;
    }

    /**
     * Updates the queue status (open/closed) for a business.
     *
     * @param int $businessId The ID of the business.
     * @param string $status The new status ('open' or 'closed').
     * @return bool True on success, false on failure.
     */
    public function updateQueueStatus(int $businessId, string $status)
    {
        $stmt = mysqli_prepare($this->conn, "UPDATE businesses SET queue_status = ? WHERE id = ?");
        if (!$stmt) {
            throw new mysqli_sql_exception("Failed to prepare statement: " . mysqli_error($this->conn));
        }
        mysqli_stmt_bind_param($stmt, "si", $status, $businessId);
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $success;
    }
}
