<?php
include '../../../Database/DatabaseConnection.php'; // Include database connection

// Set PHP timezone to match your server's timezone
date_default_timezone_set('Asia/Kuala_Lumpur'); // Adjust as per your timezone

// Initialize variables
$event_active = false;

// Check for an active event named "Nomination Vote"
$current_date = date('Y-m-d H:i:s');
$check_event_sql = "SELECT EventID FROM VSEvents WHERE EventName = 'Nomination Vote' AND IsActive = 1 AND StartDate <= ? AND EndDate >= ?";
$stmt_check_event = $conn->prepare($check_event_sql);
$stmt_check_event->bind_param("ss", $current_date, $current_date);
$stmt_check_event->execute();
$result_event = $stmt_check_event->get_result();

if ($result_event->num_rows > 0) {
    $event_active = true; // Set to true if event is active
}

$stmt_check_event->close();
$conn->close(); // Close connection to the database
?>
