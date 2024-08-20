<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['selected_users']) && isset($_POST['bulk_action'])) {
    $selectedUsers = $_POST['selected_users'];
    $bulkAction = $_POST['bulk_action'];

    // Connect to database
    $conn = new mysqli("localhost", "root", "", "VotingSystem");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    foreach ($selectedUsers as $userID) {
        $userID = $conn->real_escape_string($userID); // Escape special characters

        switch ($bulkAction) {
            case 'Approve Selected':
                $sql = "UPDATE VSStudents SET UserApproval = 1 WHERE StudentID = '$userID'";
                break;
            case 'Unapprove Selected':
                $sql = "UPDATE VSStudents SET UserApproval = 0 WHERE StudentID = '$userID'";
                break;
            case 'Delete Selected':
                // First delete from related tables
                $sql = "DELETE FROM VSCandidateVote WHERE StudentID = '$userID'";
                if (!$conn->query($sql)) {
                    echo "Error deleting from VSCandidateVote: " . $conn->error;
                }

                $sql = "DELETE FROM VSSRCVote WHERE StudentID = '$userID'";
                if (!$conn->query($sql)) {
                    echo "Error deleting from VSSRCVote: " . $conn->error;
                }

                $sql = "DELETE FROM VSApprovedCandidates WHERE StudentID = '$userID'";
                if (!$conn->query($sql)) {
                    echo "Error deleting from VSApprovedCandidates: " . $conn->error;
                }

                $sql = "DELETE FROM VSApprovedSRC WHERE StudentID = '$userID'";
                if (!$conn->query($sql)) {
                    echo "Error deleting from VSApprovedSRC: " . $conn->error;
                }

                // Then delete the user
                $sql = "DELETE FROM VSStudents WHERE StudentID = '$userID'";
                if (!$conn->query($sql)) {
                    echo "Error deleting from VSStudents: " . $conn->error;
                }
                break;
            default:
                echo "Invalid bulk action.";
                exit();
        }

        // Check if update was successful
        if ($bulkAction == 'Approve Selected' || $bulkAction == 'Unapprove Selected') {
            if (!$conn->query($sql)) {
                echo "Error updating user approval: " . $conn->error;
            }
        }
    }

    $conn->close();
    header("Location: ManageUsers.php"); // Redirect back to the page
    exit();
} else {
    echo "No users selected or no action specified.";
}
?>
