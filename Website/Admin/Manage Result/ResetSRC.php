<?php
// Database connection details
include '../../../Database/DatabaseConnection.php';

// SQL to update SRCApproval to 0 for all candidates in the VSVote table
$sql_reset_src_approval = "UPDATE VSVote SET SRCApproval = 0";

if ($conn->query($sql_reset_src_approval) === TRUE) {
    echo '<script>alert("All approved candidates entries have been deleted."); window.location.href = "ManageResult.php";</script>';
} else {
    echo "Error deleting from VSApprovedSRC: " . $conn->error;
}

$conn->close();
?>
