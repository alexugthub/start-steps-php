<?php
require_once '../db.php'; // Adjust path to db.php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['title']) && isset($_POST['content'])) {
        $title = trim($_POST['title']);
        $content = trim($_POST['content']);

        if (!empty($title) && !empty($content)) {
            // Prepare an insert statement
            $sql = "INSERT INTO notes (title, content) VALUES (?, ?)";

            if ($stmt = $conn->prepare($sql)) {
                // Bind variables to the prepared statement as parameters
                $stmt->bind_param("ss", $param_title, $param_content);

                // Set parameters
                $param_title = $title;
                $param_content = $content;

                // Attempt to execute the prepared statement
                if ($stmt->execute()) {
                    // Redirect back to the main page with success status
                    header("location: ../index.php?status=added");
                    exit();
                } else {
                    // Log error: $stmt->error;
                    header("location: ../index.php?error=dberror");
                    exit();
                }

                // Close statement
                $stmt->close();
            } else {
                // Log error: $conn->error;
                header("location: ../index.php?error=preparefail");
                exit();
            }
        } else {
            // Handle empty title or content
            header("location: ../index.php?error=emptyfields");
            exit();
        }
    } else {
        // Handle missing title or content fields
        header("location: ../index.php?error=missingfields");
        exit();
    }
} else {
    // If not a POST request, redirect to index or show an error
    header("location: ../index.php");
    exit();
}

// Close connection - db.php might be included again on redirect, 
// but it's good practice if this script could be run standalone.
if (isset($conn)) {
    $conn->close();
}
?>
