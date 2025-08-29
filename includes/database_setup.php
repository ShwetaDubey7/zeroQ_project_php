<?php
// Includes the database connection configuration.
require_once 'db.php';

echo "<pre style='font-family: monospace; white-space: pre-wrap;'>";
echo "Attempting to set up database...\n";

// Use the $conn (MySQLi) connection from db.php

// SQL statement to create the 'businesses' table
$sql_businesses = "
CREATE TABLE IF NOT EXISTS `businesses` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `business_name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `queue_code` VARCHAR(10) NOT NULL UNIQUE,
  `queue_status` ENUM('open', 'closed') NOT NULL DEFAULT 'closed',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";

// Execute the query for 'businesses' table
if (mysqli_query($conn, $sql_businesses)) {
    echo "SUCCESS: 'businesses' table created or already exists.\n";
} else {
    die("ERROR: Could not create 'businesses' table. " . mysqli_error($conn) . "\n");
}

//SQL statement to create the 'users' table
$sql_users = "
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; ";

// Execute the query for 'users' table
if (mysqli_query($conn, $sql_users)) {
    echo "SUCCESS: 'users' table created or already exists.\n";
} else {
    die("ERROR: Could not create 'users' table. " . mysqli_error($conn) . "\n");
}

// SQL statement to create the 'queues' table
$sql_queues = "
CREATE TABLE IF NOT EXISTS `queues` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `business_id` INT UNSIGNED NOT NULL,
  `customer_name` VARCHAR(255) NOT NULL,
  `token_number` INT UNSIGNED NOT NULL,
  `status` ENUM('waiting', 'serving', 'completed', 'cancelled') NOT NULL DEFAULT 'waiting',
  `join_time` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `called_time` TIMESTAMP NULL DEFAULT NULL,
  `completed_time` TIMESTAMP NULL DEFAULT NULL,
  FOREIGN KEY (`business_id`) 
    REFERENCES `businesses`(`id`) 
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";

// Execute the query for 'queues' table
if (mysqli_query($conn, $sql_queues)) {
    echo "SUCCESS: 'queues' table created or already exists.\n";
} else {
    die("ERROR: Could not create 'queues' table. " . mysqli_error($conn) . "\n");
}

// Sql statement for invitations table
$sql_invitations = "
CREATE TABLE IF NOT EXISTS `invitations` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `business_id` INT UNSIGNED NOT NULL,
  `user_id` INT UNSIGNED NOT NULL,
  `status` ENUM('pending', 'accepted') NOT NULL DEFAULT 'pending',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`business_id`) REFERENCES `businesses`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";

// Execute the query for 'invitations' table
if (mysqli_query($conn, $sql_invitations)) {
    echo "SUCCESS: 'invitations' table created or already exists.\n";
} else {
    die("ERROR: Could not create 'invitations' table. " . mysqli_error($conn) . "\n");
}

echo "\nDatabase setup complete!";

// Close the connection (optional here, as script ends, but good practice)
mysqli_close($conn);
?>