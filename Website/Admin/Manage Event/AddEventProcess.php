<?php
// Start a session
session_start();
// Include the database connection file
include '../../../Database/DatabaseConnection.php';
// Check if the request method is POST and the required fields are set
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['EventName']) && isset($_POST['StartDate']) && isset($_POST['EndDate'])) {
    // Retrieve and sanitize input data
    $EventName = $_POST['EventName'];
    $StartDate = $_POST['StartDate'];
    $EndDate = $_POST['EndDate'];
    // Prepare the SQL statement to insert the event into the database
    $insert_event_sql = "INSERT INTO VSEvents (EventName, IsActive, StartDate, EndDate) VALUES (?, FALSE, ?, ?)";
    $stmt_insert_event = $conn->prepare($insert_event_sql);
    // Bind parameters to the SQL statement
    $stmt_insert_event->bind_param("sss", $EventName, $StartDate, $EndDate);
    // Execute the statement and check for success
    if ($stmt_insert_event->execute()) {
        // On success, alert the user and redirect to ManageEvents page
        echo '<script>alert("Event added successfully."); window.location.href = "ManageEvents.php";</script>';
        exit; // Exit to prevent further script execution
    } else {
        // On failure, display the error message
        echo "Failed to add event: " . $stmt_insert_event->error;
    }
    // Close the statement
    $stmt_insert_event->close();
}
// Close the database connection
$conn->close();
?>
