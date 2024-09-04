<?php
// Database connection details
include '../../../Database/DatabaseConnection.php';

// SQL to delete all entries from VSApprovedSRC table
$sql_delete_src = "DELETE FROM VSCurrentSRC";

if ($conn->query($sql_delete_src) === TRUE) {
    echo '<script>alert("All approved SRC entries have been deleted."); window.location.href = "ManageResult.php";</script>';
} else {
    echo "Error deleting from VSApprovedSRC: " . $conn->error;
}

$conn->close();
?>
