<?php
// Start a session
session_start();
// Include the database connection file
include '../../../Database/DatabaseConnection.php';

// Check if the request method is POST and the EventID is set
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['EventID'])) {
    // Retrieve the EventID from the POST data
    $EventID = $_POST['EventID'];

    // Initialize an array to store the fields to update
    $fields_to_update = [];
    $parameters = [];
    $types = "";

    // Check if each field is set and add it to the update query
    if (isset($_POST['StartDate'])) {
        $fields_to_update[] = "StartDate = ?";
        $parameters[] = $_POST['StartDate'];
        $types .= "s";
    }
    if (isset($_POST['EndDate'])) {
        $fields_to_update[] = "EndDate = ?";
        $parameters[] = $_POST['EndDate'];
        $types .= "s";
    }
    if (isset($_POST['VoteLimit'])) {
        $fields_to_update[] = "VoteLimit = ?";
        $parameters[] = (int)$_POST['VoteLimit'];
        $types .= "i";
    }
    if (isset($_POST['EventName'])) {
        $fields_to_update[] = "EventName = ?";
        $parameters[] = $_POST['EventName'];
        $types .= "s";
    }
    if (isset($_POST['IsActive'])) {
        $fields_to_update[] = "IsActive = ?";
        $parameters[] = (int)$_POST['IsActive'];
        $types .= "i";
    }

    // If there are no fields to update, exit
    if (empty($fields_to_update)) {
        echo "No valid data provided for updating.";
        exit();
    }

    // Construct the SQL query dynamically
    $update_event_sql = "UPDATE VSEvents SET " . implode(", ", $fields_to_update) . " WHERE EventID = ?";
    $parameters[] = (int)$EventID;
    $types .= "i";

    // Prepare the statement
    $stmt = $conn->prepare($update_event_sql);

    // Bind the parameters dynamically
    $stmt->bind_param($types, ...$parameters);

    // Execute the statement
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
