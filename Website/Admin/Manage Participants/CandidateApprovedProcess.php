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
        // Check if the candidate already exists in VSCurrentCandidate
        $stmtCheck = $conn->prepare("SELECT COUNT(*) FROM VSCurrentCandidate WHERE StudentID = ?");
        $stmtCheck->bind_param("s", $studentID);
        $stmtCheck->execute();
        $stmtCheck->bind_result($count);
        $stmtCheck->fetch();
        $stmtCheck->close();

        if ($count > 0) {
            // Candidate already exists
            echo "<script>alert('The desired user already approved'); window.location.href = 'ManageParticipants.php';</script>";
            $conn->rollback();
            exit();
        }

        // Prepare the SQL statement to insert the candidate into VSCurrentCandidate
        $stmtInsert = $conn->prepare("INSERT INTO VSCurrentCandidate (StudentID, StudentEmail, StudentName, StudentProfilePicture)
            SELECT StudentID, StudentEmail, StudentName, StudentProfilePicture
            FROM VSStudents
            WHERE StudentID = ?");
        
        // Bind parameters
        $stmtInsert->bind_param("s", $studentID);

        // Execute the insert statement
        if (!$stmtInsert->execute()) {
            throw new Exception("Error inserting candidate: " . $stmtInsert->error);
        }

        // Prepare the SQL statement to update NominationApproval in VSStudents
        $stmtUpdate = $conn->prepare("UPDATE VSStudents SET NominationApproval = 1 WHERE StudentID = ?");
        $stmtUpdate->bind_param("s", $studentID);

        // Execute the update statement
        if (!$stmtUpdate->execute()) {
            throw new Exception("Error updating NominationApproval: " . $stmtUpdate->error);
        }

        // Commit transaction
        $conn->commit();

        // Redirect back to ManageParticipants.php
        echo "<script>window.location.href = 'ManageParticipants.php';</script>";
    } catch (Exception $e) {
        // Rollback transaction in case of error
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }

    // Close the statements
    $stmtInsert->close();
    $stmtUpdate->close();
} else {
    echo "Invalid request method.";
}

$conn->close();
?>
