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
        // On success, alert the user and redirect to ManageEvents page
        echo '<script>alert("Event dates reset successfully."); window.location.href = "ManageEvents.php";</script>';
        exit; // Exit to prevent further script execution
    } else {
        // On failure, display the error message
        echo "Failed to reset event dates: " . $stmt_reset_event->error;
    }
    // Close the statement
    $stmt_reset_event->close();
}
// Close the database connection
$conn->close();
?>
