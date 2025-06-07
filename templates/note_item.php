<?php
// Template to display a single note item
// Variables available: $note_id, $note_title, $note_content, $note_created_at
?>
<div class="note-item">
    <h3><?php echo htmlspecialchars($note_title); ?></h3>
    <div class="note-content">
        <?php echo $note_content; // Already sanitized and nl2br'd in index.php ?>
    </div>
    <p class="note-meta">Created on: <?php echo date('Y-m-d H:i:s', strtotime($note_created_at)); ?></p>
    <div class="note-actions">
        <a href="edit_note.php?id=<?php echo $note_id; ?>" class="edit-button">Edit</a>
        <form action="actions/delete_note.php" method="POST" style="display:inline;">
            <input type="hidden" name="note_id" value="<?php echo $note_id; ?>">
            <button type="submit" onclick="return confirm('Are you sure you want to delete this note?');">Delete</button>
        </form>
    </div>
</div>
<hr class="note-divider">
