<?php
// Check if the request is POST and if a bulk action is specified
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['bulk_action'])) {
    // Check if any users are selected; if not, show an alert and redirect
    if (!isset($_POST['selected_users']) || empty($_POST['selected_users'])) {
        echo "<script>alert('No users selected or no action specified.'); window.location.href = 'ManageUsers.php';</script>";
        exit();
    }
    // Get selected users and bulk action
    $selectedUsers = $_POST['selected_users'];
    $bulkAction = $_POST['bulk_action'];
    // Include the database connection
    include '../../../Database/DatabaseConnection.php';
    // Loop through each selected user and perform the appropriate action
    foreach ($selectedUsers as $userID) {
        $userID = $conn->real_escape_string($userID); // Escape user ID to prevent SQL injection
        // Switch based on the bulk action (approve, unapprove, delete)
        switch ($bulkAction) {
            case 'Approve Selected':
                // Approve the selected user
                $sql = "UPDATE VSStudents SET UserApproval = 1 WHERE StudentID = '$userID'";
                break;
            case 'Unapprove Selected':
                // Unapprove the selected user
                $sql = "UPDATE VSStudents SET UserApproval = 0 WHERE StudentID = '$userID'";
                break;
            case 'Delete Selected':
                // Delete the user's voting data from VSVote table
                $sql = "DELETE FROM VSVote WHERE StudentID = '$userID'";
                if (!$conn->query($sql)) {
                    echo "Error deleting from VSVote: " . $conn->error;
                }
                // Delete the user's voting history from VSVoteHistory table
                $sql = "DELETE FROM VSVoteHistory WHERE VoterID = '$userID'";
                if (!$conn->query($sql)) {
                    echo "Error deleting from VSCurrentSRC: " . $conn->error;
                }
                // Finally, delete the user from the VSStudents table
                $sql = "DELETE FROM VSStudents WHERE StudentID = '$userID'";
                if (!$conn->query($sql)) {
                    echo "Error deleting from VSStudents: " . $conn->error;
                }
                break;
            default:
                // Handle invalid actions
                echo "Invalid bulk action.";
                exit();
        }
        // If the action is approve or unapprove, execute the update query
        if ($bulkAction == 'Approve Selected' || $bulkAction == 'Unapprove Selected') {
            if (!$conn->query($sql)) {
                echo "Error updating user approval: " . $conn->error;
            }
        }
    }
    // Close the database connection
    $conn->close();
    // Redirect back to the ManageUsers page
    header("Location: ManageUsers.php");
    exit();
} else {
    // If the request method is not POST or no bulk action, redirect to ManageUsers page
    header("Location: ManageUsers.php");
    exit();
}
?>
