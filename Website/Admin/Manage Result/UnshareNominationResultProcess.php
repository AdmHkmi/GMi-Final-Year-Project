<?php
// Start the session and check if the admin is logged in
session_start();

include '../../../Database/DatabaseConnection.php';

try {
    // Begin transaction for atomic operations
    $conn->begin_transaction();

    // Check if IsActive is already false for "Nomination Result"
    $checkStmt = $conn->prepare("SELECT IsActive FROM VSEvents WHERE EventName = 'Nomination Result'");
    $checkStmt->execute();
    $checkStmt->bind_result($isActive);
    $checkStmt->fetch();
    $checkStmt->close();

    if (!$isActive) {
        echo '<script>alert("The results have not been shared yet."); window.location.href = "ManageResult.php";</script>';
        exit();
    }

    // Update IsActive for "Nomination Result" event to FALSE (0)
    $updateStmt = $conn->prepare("UPDATE VSEvents SET IsActive = FALSE WHERE EventName = 'Nomination Result'");
    $updateStmt->execute();

    if ($updateStmt->affected_rows > 0) {
        // Commit transaction if update was successful
        $conn->commit();
        echo '<script>alert("Nomination Results have been unshared successfully."); window.location.href = "ManageResult.php";</script>';
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
    echo '<script>alert("Error unsharing Nomination Results: ' . $e->getMessage() . '"); window.location.href = "ManageResult.php";</script>';
}

// Close connection
$conn->close();
?>
