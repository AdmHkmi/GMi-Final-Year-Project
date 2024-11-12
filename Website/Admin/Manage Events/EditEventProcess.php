<?php
// Start the session and include the database connection
session_start();
include '../../../Database/DatabaseConnection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $eventID = $_POST['EventID'];
    $startDate = $_POST['StartDate'];
    $endDate = $_POST['EndDate'];
    $voteLimit = isset($_POST['VoteLimit']) ? $_POST['VoteLimit'] : null;

    // Prepare the update query
    $update_sql = "UPDATE VSEvents SET StartDate = ?, EndDate = ?, VoteLimit = ? WHERE EventID = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("ssii", $startDate, $endDate, $voteLimit, $eventID);

    // Execute and check for success
    if ($stmt->execute()) {
        echo "<script>alert('Event updated successfully'); window.location.href = 'ManageEvents.php';</script>";
    } else {
        echo "<script>alert('Error updating event'); window.location.href = 'ManageEvents.php';</script>";
    }
    $stmt->close();
    $conn->close();
}
?>
