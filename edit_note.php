<?php
require_once 'db.php';

$note_id = null;
$note_title = '';
$note_content = '';
$error_message = '';

if (isset($_GET['id']) && filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    $note_id = $_GET['id'];

    // Fetch the note from the database
    $sql = "SELECT title, content FROM notes WHERE id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $note_id);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($result->num_rows == 1) {
                $note = $result->fetch_assoc();
                $note_title = $note['title'];
                $note_content = $note['content'];
            } else {
                $error_message = "Note not found.";
            }
        } else {
            $error_message = "Error fetching note: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $error_message = "Error preparing statement: " . $conn->error;
    }
} else {
    $error_message = "Invalid note ID provided.";
}

// If there was an error fetching (e.g. note not found, invalid ID), 
// and we don't want to show the form, we could redirect or die here.
// For now, we'll let the form show, but it will be empty or show an error message.

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Note</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Edit Note</h1>

        <?php if (!empty($error_message)): ?>
            <p class="error-message"><?php echo htmlspecialchars($error_message); ?></p>
            <p><a href="index.php">Back to Notes List</a></p>
        <?php endif; ?>

        <?php if (empty($error_message) && $note_id): // Only show form if note was loaded successfully ?>
        <form action="actions/update_note.php" method="POST" class="note-form">
            <input type="hidden" name="note_id" value="<?php echo htmlspecialchars($note_id); ?>">
            <div>
                <label for="title">Title:</label>
                <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($note_title); ?>" required>
            </div>
            <div>
                <label for="content">Content:</label>
                <textarea id="content" name="content" rows="10" required><?php echo htmlspecialchars($note_content); ?></textarea>
            </div>
            <div>
                <button type="submit">Update Note</button>
                <a href="index.php" class="cancel-button">Cancel</a>
            </div>
        </form>
        <?php elseif (empty($error_message) && !$note_id):
            // This case should ideally not be reached if ID is always required and validated.
            // But as a fallback if $error_message is empty but $note_id is not set.
        ?>
             <p class="error-message">No note ID specified.</p>
             <p><a href="index.php">Back to Notes List</a></p>
        <?php endif; ?>

    </div>
</body>
</html>
<?php
if (isset($conn)) {
    $conn->close();
}
?>
