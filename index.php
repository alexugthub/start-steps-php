<?php

require_once 'db.php';

$feedback_message = '';
$message_type = ''; // 'success' or 'error'

// Check for feedback messages from actions
if (isset($_GET['status'])) {
    switch ($_GET['status']) {
        case 'added':
            $feedback_message = 'Note successfully added!';
            $message_type = 'success';
            break;
        case 'updated':
            $feedback_message = 'Note successfully updated!';
            $message_type = 'success';
            break;
        case 'deleted':
            $feedback_message = 'Note successfully deleted!';
            $message_type = 'success';
            break;
    }
} elseif (isset($_GET['error'])) {
    $message_type = 'error';
    switch ($_GET['error']) {
        case 'emptyfields':
            $feedback_message = 'Error: Title and content cannot be empty.';
            break;
        case 'missingfields':
            $feedback_message = 'Error: Required form fields were missing.';
            break;
        case 'dberror':
            $feedback_message = 'Error: A database error occurred. Please try again.';
            break;
        case 'preparefail':
            $feedback_message = 'Error: Could not prepare the database statement.';
            break;
        case 'invalidid':
            $feedback_message = 'Error: Invalid ID provided for the note.';
            break;
        case 'missingid':
            $feedback_message = 'Error: Note ID was missing.';
            break;
        default:
            $feedback_message = 'An unknown error occurred.';
            break;
    }
}

// Fetch all notes
$sql_fetch_notes = "SELECT id, title, content, created_at FROM notes ORDER BY created_at DESC";
$result = $conn->query($sql_fetch_notes);
$notes = [];
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $notes[] = $row;
    }
}

// Main application logic will go here - e.g., handling form submissions if not done in separate action files
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Notes</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>My Notes</h1>

        <?php if (!empty($feedback_message)): ?>
            <div class="<?php echo ($message_type === 'success') ? 'success-message' : 'error-message'; ?>">
                <?php echo htmlspecialchars($feedback_message); ?>
            </div>
        <?php endif; ?>

        <!-- Form to add a new note -->
        <form action="actions/add_note.php" method="POST" class="note-form">
            <h2>Add a New Note</h2>
            <div>
                <label for="title">Title:</label>
                <input type="text" id="title" name="title" required>
            </div>
            <div>
                <label for="content">Content:</label>
                <textarea id="content" name="content" rows="5" required></textarea>
            </div>
            <div>
                <button type="submit">Add Note</button>
            </div>
        </form>

        <hr>

        <!-- Display existing notes -->
        <h2>Existing Notes</h2>
        <div class="notes-list">
            <?php if (!empty($notes)): ?>
                <?php foreach ($notes as $note): ?>
                    <?php 
                        // Pass note data to the template
                        $note_id = $note['id'];
                        $note_title = $note['title'];
                        $note_content = nl2br(htmlspecialchars($note['content'])); // Sanitize and format content
                        $note_created_at = $note['created_at'];
                        include 'templates/note_item.php'; 
                    ?>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No notes yet. Add one above!</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
<?php
$conn->close();
?>
