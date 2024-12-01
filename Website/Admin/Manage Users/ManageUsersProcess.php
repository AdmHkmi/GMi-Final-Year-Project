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

    switch ($bulkAction) {
        case 'Approve Selected':
            $sql = "UPDATE VSStudents SET UserApproval = 1 WHERE StudentID = '$userID'";
            if (!$conn->query($sql)) {
                echo "Error updating user approval: " . $conn->error;
                continue 2; // Skip to the next user
            }
            $insertVoteSql = "INSERT INTO VSVote (StudentID, StudentEmail, StudentName, StudentProfilePicture)
                              SELECT StudentID, StudentEmail, StudentName, StudentProfilePicture
                              FROM VSStudents
                              WHERE StudentID = '$userID'";
            if (!$conn->query($insertVoteSql)) {
                echo "Error inserting into VSVote: " . $conn->error;
            }
            break;

        case 'Unapprove Selected':
            $sql = "UPDATE VSStudents SET UserApproval = 0 WHERE StudentID = '$userID'";
            if (!$conn->query($sql)) {
                echo "Error updating user unapproval: " . $conn->error;
                continue 2; // Skip to the next user
            }
            $deleteVoteSql = "DELETE FROM VSVote WHERE StudentID = '$userID'";
            if (!$conn->query($deleteVoteSql)) {
                echo "Error deleting from VSVote: " . $conn->error;
            }
            $deleteVoteHistorySql = "DELETE FROM VSVoteHistory WHERE VoterID = '$userID'";
            if (!$conn->query($deleteVoteHistorySql)) {
                echo "Error deleting from VSVoteHistory: " . $conn->error;
            }
            break;

        case 'Delete Selected':
            $deleteVoteSql = "DELETE FROM VSVote WHERE StudentID = '$userID'";
            if (!$conn->query($deleteVoteSql)) {
                echo "Error deleting from VSVote: " . $conn->error;
            }
            $deleteVoteHistorySql = "DELETE FROM VSVoteHistory WHERE VoterID = '$userID'";
            if (!$conn->query($deleteVoteHistorySql)) {
                echo "Error deleting from VSVoteHistory: " . $conn->error;
            }
            $sql = "DELETE FROM VSStudents WHERE StudentID = '$userID'";
            if (!$conn->query($sql)) {
                echo "Error deleting from VSStudents: " . $conn->error;
            }
            break;

        default:
            echo "Invalid bulk action.";
            exit();
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
