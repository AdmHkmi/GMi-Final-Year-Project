<?php
// Start the session and check if the admin is logged in
session_start();

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

// Handle form submission to delete event
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['deleteEventID'])) {
    $deleteEventID = $_POST['deleteEventID'];

    $delete_event_sql = "DELETE FROM VSEvents WHERE EventID = ?";
    $stmt_delete_event = $conn->prepare($delete_event_sql);
    $stmt_delete_event->bind_param("i", $deleteEventID);
    
    if ($stmt_delete_event->execute()) {
        echo '<script>alert("Event deleted successfully."); window.location.href = "ManageEvents.php";</script>';
        exit; // Exit after successful deletion
    } else {
        echo "Failed to delete event: " . $stmt_delete_event->error;
    }
    
    $stmt_delete_event->close();
}

$conn->close();
?>
