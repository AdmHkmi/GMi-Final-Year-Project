<?php
// Start the session and check if the admin is logged in

include '../../../Database/DatabaseConnection.php';

// Handle form submission to reset event dates
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ResetEvent'])) {
    $ResetEvent = $_POST['ResetEvent'];

    // Reset event dates in the Events table
    $reset_event_sql = "UPDATE VSEvents SET StartDate = NULL, EndDate = NULL WHERE EventID = ?";
    $stmt_reset_event = $conn->prepare($reset_event_sql);
    $stmt_reset_event->bind_param("i", $ResetEvent);
    
    if ($stmt_reset_event->execute()) {
        echo '<script>alert("Event dates reset successfully."); window.location.href = "ManageEvents.php";</script>';
        exit; // Exit after successful update
    } else {
        echo "Failed to reset event dates: " . $stmt_reset_event->error;
    }
    
    $stmt_reset_event->close();
}

$conn->close();
?>
