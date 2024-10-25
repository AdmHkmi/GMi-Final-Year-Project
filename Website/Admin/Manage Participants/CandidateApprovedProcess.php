<?php
include '../../../Database/DatabaseConnection.php';

// Check if the form is submitted and if the candidate's StudentID is provided
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['StudentID'])) {
    $studentID = $_POST['StudentID']; // Retrieve the StudentID from the form

    // Prepare the SQL query to update CandidateApproval to TRUE
    $sql = "UPDATE VSVote SET CandidateApproval = TRUE WHERE StudentID = ?";

    // Prepare and bind the statement
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $studentID); // "s" indicates the parameter is a string

        // Execute the statement
        if ($stmt->execute()) {
            // If the update is successful, redirect back to the candidate list or a success page
            echo '<script>alert("Candidate approved successfully!"); window.location.href = "ManageParticipants.php";</script>';
        } else {
            // If the update fails, display an error
            echo '<script>alert("Error approving candidate: ' . $conn->error . '"); window.location.href = "ManageParticipants.php";</script>';
        }

        // Close the statement
        $stmt->close();
    } else {
        // If the statement preparation fails, display an error
        echo '<script>alert("Error preparing query: ' . $conn->error . '"); window.location.href = "ManageParticipants.php";</script>';
    }
} else {
    // If the form is not submitted correctly, redirect to the candidate list
    echo '<script>alert("Invalid request!"); window.location.href = "ManageParticipants.php";</script>';
}

// Close the database connection
$conn->close();
?>
