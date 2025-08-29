<?php
// --- Database Configuration ---
// Defines database connection details here!
define('DB_HOST', 'localhost');      // The server where your database is hosted.
define('DB_NAME', 'zeroq_db');       // The name of your database.
define('DB_USER', 'root');           // Your database username (default for XAMPP is 'root').
define('DB_PASS', '');               // Your database password (default for XAMPP is empty).
define('DB_CHARSET', 'utf8mb4');     // The character set for the connection.

// Create a new MySQLi instance.
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if (!$conn) {
    // If the connection fails, stop the script and display an error message.
    die("Database connection failed: " . mysqli_connect_error());
}

// Set the character set for the connection
if (!mysqli_set_charset($conn, DB_CHARSET)) {
    die("Error loading character set " . DB_CHARSET . ": " . mysqli_error($conn));
}

// If the script reaches this point, the connection was successful.
// The $conn variable can now be used in other scripts to interact with the database.

?>