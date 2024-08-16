<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "VotingSystem";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission to reset event dates and delete candidate votes
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ResetEvent'])) {
    $ResetEvent = $_POST['ResetEvent'];

    // Reset event dates in the Events table
    $reset_event_sql = "UPDATE VSEvents SET StartDate = NULL, EndDate = NULL WHERE EventID = ?";
    $stmt_reset_event = $conn->prepare($reset_event_sql);
    $stmt_reset_event->bind_param("i", $ResetEvent);
    
    if ($stmt_reset_event->execute()) {
        // Check if the event is inactive
        $check_event_status_sql = "SELECT IsActive FROM VSEvents WHERE EventID = ?";
        $stmt_check_status = $conn->prepare($check_event_status_sql);
        $stmt_check_status->bind_param("i", $ResetEvent);
        $stmt_check_status->execute();
        $stmt_check_status->bind_result($isActive);
        $stmt_check_status->fetch();
        $stmt_check_status->close();
        
        // If IsActive is 0 (Inactive) and StartDate/EndDate are NULL, display alert
        if ($isActive == 0) {
            echo '<script>alert("Failed to reset. This event is not active yet."); window.location.href = "ManageEvents.php";</script>';
            exit; // Exit after displaying alert
        }
        
        // Reset VoteCount and NominationVoteStatus for all users in svuser table
        $reset_users_sql = "UPDATE VSStudents SET TotalCandidateVote = 0, NominationVoteStatus = 0 , NominationApproval = 0";
        if ($conn->query($reset_users_sql) === TRUE) {
            // Delete records from CandidateVote table
            $delete_candidate_vote_sql = "DELETE FROM VSCandidateVote";
            if ($conn->query($delete_candidate_vote_sql) === TRUE) {
                echo '<script>alert("Event reset successfully."); window.location.href = "ManageEvents.php";</script>';
                exit; // Exit after successful update and deletion
            } else {
                echo "Failed to delete candidate votes: " . $conn->error;
            }
        } else {
            echo "Failed to reset vote counts and nomination statuses: " . $conn->error;
        }
    } else {
        echo "Failed to reset event dates: " . $stmt_reset_event->error;
    }
    
    $stmt_reset_event->close();
}

$conn->close();
?>
