<?php
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

// Get the StudentID from the form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $studentID = $_POST['candidate_name'];

    // Validate StudentID
    if (empty($studentID)) {
        echo "No StudentID provided.";
        exit();
    }

    // Begin transaction
    $conn->begin_transaction();

    try {
        // Prepare the SQL statement to delete the candidate from VSCurrentCandidate
        $stmtDelete = $conn->prepare("DELETE FROM VSCurrentCandidate WHERE StudentID = ?");
        $stmtDelete->bind_param("s", $studentID);

        // Execute the delete statement
        if (!$stmtDelete->execute()) {
            throw new Exception("Error deleting candidate: " . $stmtDelete->error);
        }

        // Prepare the SQL statement to update NominationApproval in VSStudents
        $stmtUpdate = $conn->prepare("UPDATE VSStudents SET NominationApproval = 0 WHERE StudentID = ?");
        $stmtUpdate->bind_param("s", $studentID);

        // Execute the update statement
        if (!$stmtUpdate->execute()) {
            throw new Exception("Error updating NominationApproval: " . $stmtUpdate->error);
        }

        // Commit transaction
        $conn->commit();

        // Redirect back to ManageCandidates.php
        echo "<script>alert('Candidate unapproved successfully!');window.location.href = 'ManageParticipants.php';</script>";
    } catch (Exception $e) {
        // Rollback transaction in case of error
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }

    // Close the statements
    $stmtDelete->close();
    $stmtUpdate->close();
} else {
    echo "Invalid request method.";
}

$conn->close();
?>
