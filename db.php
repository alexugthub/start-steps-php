<?php
// Database connection and functions will go here
$servername = "localhost";
$username = "alexu";
$password = "AP|~hd6]8e([w$){";
$dbname = "start_steps";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Attempt to select the database
if (!$conn->select_db($dbname)) {
    // If the database doesn't exist, try to create it
    $sql_create_db = "CREATE DATABASE IF NOT EXISTS $dbname";
    if ($conn->query($sql_create_db) === TRUE) {
        // echo "Database created successfully or already exists.\n";
        $conn->select_db($dbname); // Select the database again after creation
    } else {
        die("Error creating database: " . $conn->error);
    }
}

// Function to create the notes table
function createNotesTable($conn) {
    $sql = "CREATE TABLE IF NOT EXISTS notes (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        content TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";

    if ($conn->query($sql) === TRUE) {
        // echo "Table 'notes' created successfully or already exists.\n";
    } else {
        echo "Error creating table: " . $conn->error . "\n";
    }
}

// Create the notes table if it doesn't exist
createNotesTable($conn);

// You can add other database related functions here (e.g., for fetching, inserting, updating, deleting notes)

?>
