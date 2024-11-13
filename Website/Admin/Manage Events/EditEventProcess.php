<?php
// Start a session
session_start();
// Include the database connection file
include '../../../Database/DatabaseConnection.php';
// Check if the request method is POST and the EventID is set
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['EventID'])) {
    // Retrieve the EventID from the POST data
    $EventID = $_POST['EventID'];

    // Check if IsActive status is being updated
    if (isset($_POST['IsActive'])) {
        $IsActive = $_POST['IsActive'];
        // Prepare SQL statement to update IsActive status
        $update_event_sql = "UPDATE VSEvents SET IsActive = ? WHERE EventID = ?";
        $stmt = $conn->prepare($update_event_sql);
        // Bind parameters: IsActive (integer) and EventID (integer)
        $stmt->bind_param("ii", $IsActive, $EventID);
    } 
    // Check if StartDate and EndDate are being updated
    elseif (isset($_POST['StartDate']) && isset($_POST['EndDate'])) {
        $StartDate = $_POST['StartDate'];
        $EndDate = $_POST['EndDate'];
        // Prepare SQL statement to update StartDate and EndDate
        $update_event_sql = "UPDATE VSEvents SET StartDate = ?, EndDate = ? WHERE EventID = ?";
        $stmt = $conn->prepare($update_event_sql);
        // Bind parameters: StartDate (string), EndDate (string), and EventID (integer)
        $stmt->bind_param("ssi", $StartDate, $EndDate, $EventID);
    } 
    // Check if EventName is being updated
    elseif (isset($_POST['EventName'])) {
        $EventName = $_POST['EventName'];
        $StartDate = $_POST['StartDate'];
        $EndDate = $_POST['EndDate'];
        $IsActive = $_POST['IsActive'];
        // Prepare SQL statement to update EventName, StartDate, EndDate, and IsActive
        $update_event_sql = "UPDATE VSEvents SET EventName = ?, StartDate = ?, EndDate = ?, IsActive = ? WHERE EventID = ?";
        $stmt = $conn->prepare($update_event_sql);
        // Bind parameters: EventName (string), StartDate (string), EndDate (string), IsActive (integer), and EventID (integer)
        $stmt->bind_param("sssii", $EventName, $StartDate, $EndDate, $IsActive, $EventID);
    } 
    // Check if VoteLimit is being updated
    elseif (isset($_POST['VoteLimit'])) {
        $VoteLimit = $_POST['VoteLimit'];
        // Prepare SQL statement to update VoteLimit
        $update_event_sql = "UPDATE VSEvents SET VoteLimit = ? WHERE EventID = ?";
        $stmt = $conn->prepare($update_event_sql);
        // Bind parameters: VoteLimit (integer) and EventID (integer)
        $stmt->bind_param("ii", $VoteLimit, $EventID);
    } else {
        // Handle the case where no valid POST data is provided
        echo "Invalid POST data received.";
        exit();
    }

    // Execute the prepared statement and check for success
    if ($stmt->execute()) {
        // On success, redirect to ManageEvents page
        header("Location: ManageEvents.php");
        exit();
    } else {
        // On failure, display the error message
        echo "Error updating event: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();
} else {
    // Handle the case where no data was submitted
    echo "No data submitted.";
}

// Close the database connection
$conn->close();
?>
