<?php
// Start the session and check if the admin is logged in
session_start();

include '../../../Database/DatabaseConnection.php';

// Handle form submission to add new event
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['EventName']) && isset($_POST['StartDate']) && isset($_POST['EndDate'])) {
    $EventName = $_POST['EventName'];
    $StartDate = $_POST['StartDate'];
    $EndDate = $_POST['EndDate'];

    $insert_event_sql = "INSERT INTO VSEvents (EventName, IsActive, StartDate, EndDate) VALUES (?, FALSE, ?, ?)";
    $stmt_insert_event = $conn->prepare($insert_event_sql);
    $stmt_insert_event->bind_param("sss", $EventName, $StartDate, $EndDate);
    
    if ($stmt_insert_event->execute()) {
        echo '<script>alert("Event added successfully."); window.location.href = "ManageEvents.php";</script>';
        exit; // Exit after successful insertion
    } else {
        echo "Failed to add event: " . $stmt_insert_event->error;
    }
    
    $stmt_insert_event->close();
}

$conn->close();
?>