<?php
require_once '../db.php'; // Adjust path to db.php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['note_id'], $_POST['title'], $_POST['content'])) {
        $note_id = trim($_POST['note_id']);
        $title = trim($_POST['title']);
        $content = trim($_POST['content']);

        if (filter_var($note_id, FILTER_VALIDATE_INT) && !empty($title) && !empty($content)) {
            // Prepare an update statement
            $sql = "UPDATE notes SET title = ?, content = ? WHERE id = ?";

            if ($stmt = $conn->prepare($sql)) {
                // Bind variables to the prepared statement as parameters
                $stmt->bind_param("ssi", $param_title, $param_content, $param_id);

                // Set parameters
                $param_title = $title;
                $param_content = $content;
                $param_id = $note_id;

                // Attempt to execute the prepared statement
                if ($stmt->execute()) {
                    // Redirect back to the main page
                    header("location: ../index.php?status=updated");
                    exit();
                } else {
                    // Log error: $stmt->error;
                    header("location: ../edit_note.php?id=" . $note_id . "&error=dberror");
                    exit();
                }

                // Close statement
                $stmt->close();
            } else {
                // Log error: $conn->error;
                header("location: ../edit_note.php?id=" . $note_id . "&error=preparefail");
                exit();
            }
        } else {
            // Handle empty title/content or invalid ID
            $error_params = [];
            if (!filter_var($note_id, FILTER_VALIDATE_INT)) {
                $error_params[] = 'invalidid';
            }
            if (empty($title)) {
                $error_params[] = 'emptytitle';
            }
            if (empty($content)) {
                $error_params[] = 'emptycontent';
            }
            header("location: ../edit_note.php?id=" . $note_id . "&error=" . implode(',', $error_params));
            exit();
        }
    } else {
        // Handle missing fields - should not happen with 'required' in form but good to have
        header("location: ../index.php?error=missingfields");
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
