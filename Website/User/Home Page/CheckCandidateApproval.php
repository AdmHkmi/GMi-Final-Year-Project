<?php
// Start the session if it hasn't already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Ensure the database connection is included
include '../../../Database/DatabaseConnection.php';

$showEditButton = false; // Default value

// Check if session variables and database connection are properly set
if ((isset($_SESSION['StudentID']) || isset($_SESSION['StudentEmail'])) && $conn && !$conn->connect_error) {
    $loggedInStudentID = isset($_SESSION['StudentID']) ? $_SESSION['StudentID'] : null;
    $loggedInStudentEmail = isset($_SESSION['StudentEmail']) ? $_SESSION['StudentEmail'] : null;
    
    // Prepare SQL to check CandidateApproval for either StudentID or StudentEmail
    $sql = "SELECT CandidateApproval FROM VSVote WHERE StudentID = ? OR StudentEmail = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) { // Check if prepare() succeeded
        $stmt->bind_param("ss", $loggedInStudentID, $loggedInStudentEmail);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($CandidateApproval);
        $stmt->fetch();

        // If CandidateApproval is 1, set $showEditButton to true
        if ($CandidateApproval == 1) {
            $showEditButton = true;
        }

        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }
} else {
    // Debugging output for troubleshooting
    if (!isset($_SESSION['StudentID']) && !isset($_SESSION['StudentEmail'])) {
        echo "Session variables 'StudentID' or 'StudentEmail' not set.";
    } elseif ($conn->connect_error) {
        echo "Connection error: " . $conn->connect_error;
    } else {
        echo "Connection error or session not set.";
    }
}

$conn->close();
?>
