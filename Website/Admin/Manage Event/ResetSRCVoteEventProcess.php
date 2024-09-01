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

// Handle form submission to reset event dates and vote counts
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
        
        // If IsActive is 0 (Inactive), display alert
        if ($isActive == 0) {
            echo '<script>alert("Failed to reset. This event is not active yet."); window.location.href = "ManageEvents.php";</script>';
            exit; // Exit after displaying alert
        }
        
        // Reset VoteCount for all approved candidates
        $reset_vote_count_sql = "UPDATE VSStudents SET TotalSRCVote = 0";
        if ($conn->query($reset_vote_count_sql) === TRUE) {
            // Reset SRCVoteStatus for all users
            $reset_src_vote_status_sql = "UPDATE VSStudents SET SRCVoteStatus = 0";
            if ($conn->query($reset_src_vote_status_sql) === TRUE) {
                // Delete records from SRCVote table
                $delete_src_vote_sql = "DELETE FROM VSSRCVote";
                if ($conn->query($delete_src_vote_sql) === TRUE) {
                    echo '<script>alert("Event reset successfully."); window.location.href = "ManageEvents.php";</script>';
                    exit; // Exit after successful update and deletion
                } else {
                    echo "Failed to delete SRC votes: " . $conn->error;
                }
            } else {
                echo "Failed to reset SRC vote status: " . $conn->error;
            }
        } else {
            echo "Failed to reset vote counts: " . $conn->error;
        }
    } else {
        echo "Failed to reset event dates: " . $stmt_reset_event->error;
    }
    
    $stmt_reset_event->close();
}

$conn->close();
?>
