<?php
require_once '../db.php'; // Adjust path to db.php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['note_id'])) {
        $note_id = trim($_POST['note_id']);

        if (!empty($note_id) && filter_var($note_id, FILTER_VALIDATE_INT)) {
            // Prepare a delete statement
            $sql = "DELETE FROM notes WHERE id = ?";

            if ($stmt = $conn->prepare($sql)) {
                // Bind variables to the prepared statement as parameters
                $stmt->bind_param("i", $param_id);

                // Set parameters
                $param_id = $note_id;

                // Attempt to execute the prepared statement
                if ($stmt->execute()) {
                    // Redirect back to the main page with success status
                    header("location: ../index.php?status=deleted");
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
            // Handle invalid or empty note_id
            header("location: ../index.php?error=invalidid");
            exit();
        }
    } else {
        // Handle missing note_id field
        header("location: ../index.php?error=missingid");
        exit();
    }
} else {
    // If not a POST request, redirect to index or show an error
    header("location: ../index.php");
    exit();
}

// Close connection
if (isset($conn)) {
    $conn->close();
}
?>
