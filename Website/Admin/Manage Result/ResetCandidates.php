<?php

include '../../../Database/DatabaseConnection.php';

// SQL to delete all records from the ApprovedCandidates table
$sql_delete_approved_candidates = "DELETE FROM VSCurrentCandidate";

// SQL to update NominationApproval to 0 for all candidates in the SVUser table
$sql_reset_nomination_approval = "UPDATE VSStudents SET NominationApproval = 0";

if ($conn->query($sql_delete_approved_candidates) === TRUE && $conn->query($sql_reset_nomination_approval) === TRUE) {
    echo "<script>alert('All approved candidates have been reset and nomination approval statuses have been updated.'); window.location.href='ManageResult.php';</script>";
} else {
    echo "Error resetting candidates: " . $conn->error;
}

$conn->close();
?>
