<?php
// Start a session
session_start();
// Include the database connection file
include '../../../Database/DatabaseConnection.php';
// Check if the request method is POST and the delete event ID is set
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['deleteEventID'])) {
    // Retrieve the event ID to be deleted
    $deleteEventID = $_POST['deleteEventID'];
    // Prepare the SQL statement to delete the event from the database
    $delete_event_sql = "DELETE FROM VSEvents WHERE EventID = ?";
    $stmt_delete_event = $conn->prepare($delete_event_sql);
    // Bind the event ID parameter to the SQL statement
    $stmt_delete_event->bind_param("i", $deleteEventID);
    // Execute the statement and check for success
    if ($stmt_delete_event->execute()) {
        // On success, alert the user and redirect to ManageEvents page
        echo '<script>alert("Event deleted successfully."); window.location.href = "ManageEvents.php";</script>';
        exit; // Exit to prevent further script execution
    } else {
        // On failure, display the error message
        echo "Failed to delete event: " . $stmt_delete_event->error;
    }
    // Close the statement
    $stmt_delete_event->close();
}
// Close the database connection
$conn->close();
?>
