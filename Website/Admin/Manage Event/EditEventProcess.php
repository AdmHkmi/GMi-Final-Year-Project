<?php
session_start();
include '../../../Database/DatabaseConnection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['EventID'])) {
    $EventID = $_POST['EventID'];

    // Determine which form was submitted (Edit form or Reset form)
    if (isset($_POST['IsActive'])) {
        // Edit form for Voting Result event (only update IsActive)
        $IsActive = $_POST['IsActive'];
        $update_event_sql = "UPDATE VSEvents SET IsActive = ? WHERE EventID = ?";
        $stmt = $conn->prepare($update_event_sql);
        $stmt->bind_param("ii", $IsActive, $EventID);
    } elseif (isset($_POST['StartDate']) && isset($_POST['EndDate'])) {
        // Edit form for Nomination Vote or SRC Vote event (update StartDate and EndDate)
        $StartDate = $_POST['StartDate'];
        $EndDate = $_POST['EndDate'];
        $update_event_sql = "UPDATE VSEvents SET StartDate = ?, EndDate = ? WHERE EventID = ?";
        $stmt = $conn->prepare($update_event_sql);
        $stmt->bind_param("ssi", $StartDate, $EndDate, $EventID);
    } elseif (isset($_POST['EventName'])) {
        // Edit form for other events (update EventName, StartDate, EndDate, IsActive)
        $EventName = $_POST['EventName'];
        $StartDate = $_POST['StartDate'];
        $EndDate = $_POST['EndDate'];
        $IsActive = $_POST['IsActive'];
        $update_event_sql = "UPDATE VSEvents SET EventName = ?, StartDate = ?, EndDate = ?, IsActive = ? WHERE EventID = ?";
        $stmt = $conn->prepare($update_event_sql);
        $stmt->bind_param("sssii", $EventName, $StartDate, $EndDate, $IsActive, $EventID);
    } else {
        // Invalid POST data scenario, handle as needed
        echo "Invalid POST data received.";
        exit();
    }

    if ($stmt->execute()) {
        header("Location: ManageEvents.php");
        exit();
    } else {
        echo "Error updating event: " . $stmt->error;
    }

    $stmt->close();
} else {
    // No valid POST data received
    echo "No data submitted.";
}

$conn->close();
?>
