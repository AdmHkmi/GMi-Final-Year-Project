<?php

include '../../../Database/DatabaseConnection.php';

// SQL to update CandidateApproval to 0 for all candidates in the VSVote table
$sql_reset_candidate_approval = "UPDATE VSVote SET CandidateApproval = 0";

if ($conn->query($sql_reset_candidate_approval) === TRUE) {
    echo "<script>alert('All approved candidates have been reset and nomination approval statuses have been updated.'); window.location.href='ManageResult.php';</script>";
} else {
    echo "Error resetting candidates: " . $conn->error;
}

$conn->close();
?>
