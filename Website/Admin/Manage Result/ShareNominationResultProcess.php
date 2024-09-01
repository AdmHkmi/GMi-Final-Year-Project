<?php
// Start the session and check if the admin is logged in
session_start();

// Database connection details
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

try {
    // Begin transaction for atomic operations
    $conn->begin_transaction();

    // Check if IsActive is already true for "Nomination Result"
    $checkStmt = $conn->prepare("SELECT IsActive FROM VSEvents WHERE EventName = 'Nomination Result'");
    $checkStmt->execute();
    $checkStmt->bind_result($isActive);
    $checkStmt->fetch();
    $checkStmt->close();

    if ($isActive) {
        echo '<script>alert("The results have already been shared. Please check the Manage Events page for details."); window.location.href = "ManageResult.php";</script>';
        exit();
    }

    // Update IsActive for "Nomination Result" event to TRUE (1)
    $updateStmt = $conn->prepare("UPDATE VSEvents SET IsActive = TRUE WHERE EventName = 'Nomination Result'");
    $updateStmt->execute();

    if ($updateStmt->affected_rows > 0) {
        // Commit transaction if update was successful
        $conn->commit();
        echo '<script>alert("Nomination Results have been shared successfully."); window.location.href = "ManageResult.php";</script>';
        exit();
    } else {
        // Rollback transaction as no rows were updated
        $conn->rollback();
        echo '<script>alert("No rows were updated. Please check the event name."); window.location.href = "ManageResult.php";</script>';
        exit();
    }

    $updateStmt->close();
} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    echo '<script>alert("Error sharing Nomination Results: ' . $e->getMessage() . '"); window.location.href = "ManageResult.php";</script>';
}

// Close connection
$conn->close();
?>
