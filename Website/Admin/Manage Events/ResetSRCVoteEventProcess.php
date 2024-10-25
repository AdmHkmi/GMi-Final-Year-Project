<?php
// Include the database connection file
include '../../../Database/DatabaseConnection.php';
// Check if the request method is POST and the ResetEvent parameter is set
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ResetEvent'])) {
    // Retrieve the EventID that needs to be reset
    $ResetEvent = $_POST['ResetEvent'];
    // Prepare the SQL statement to reset StartDate and EndDate to NULL for the specified EventID
    $reset_event_sql = "UPDATE VSEvents SET StartDate = NULL, EndDate = NULL WHERE EventID = ?";
    $stmt_reset_event = $conn->prepare($reset_event_sql);
    // Bind the EventID parameter to the SQL statement
    $stmt_reset_event->bind_param("i", $ResetEvent);
    // Execute the statement and check for success
    if ($stmt_reset_event->execute()) {
        // Prepare SQL statement to check if the event is active
        $check_event_status_sql = "SELECT IsActive FROM VSEvents WHERE EventID = ?";
        $stmt_check_status = $conn->prepare($check_event_status_sql);
        $stmt_check_status->bind_param("i", $ResetEvent);
        $stmt_check_status->execute();
        // Bind the result to the variable
        $stmt_check_status->bind_result($isActive);
        $stmt_check_status->fetch(); // Fetch the result
        $stmt_check_status->close(); // Close the status check statement
        // Check if the event is not active
        if ($isActive == 0) {
            echo '<script>alert("Failed to reset. This event is not active yet."); window.location.href = "ManageEvents.php";</script>';
            exit; // Exit to prevent further script execution
        }
        // Prepare SQL statement to reset vote counts for SRC votes
        $reset_vote_count_sql = "UPDATE VSVote SET TotalSRCVote = 0, SRCVoteLimit = 0";
        if ($conn->query($reset_vote_count_sql) === TRUE) {
            // Prepare SQL statement to delete SRC vote history
            $delete_src_vote_sql = "DELETE FROM VSVoteHistory WHERE VoteType='SRC'";
            if ($conn->query($delete_src_vote_sql) === TRUE) {
                // On success, alert the user and redirect to ManageEvents page
                echo '<script>alert("Event reset successfully."); window.location.href = "ManageEvents.php";</script>';
                exit; // Exit to prevent further script execution
            } else {
                // On failure, display the error message for deleting SRC votes
                echo "Failed to delete SRC votes: " . $conn->error;
            }
        } else {
            // On failure, display the error message for resetting vote counts
            echo "Failed to reset vote counts: " . $conn->error;
        }
    } else {
        // On failure, display the error message for resetting event dates
        echo "Failed to reset event dates: " . $stmt_reset_event->error;
    }
    // Close the statement for resetting event dates
    $stmt_reset_event->close();
}
// Close the database connection
$conn->close();
?>
