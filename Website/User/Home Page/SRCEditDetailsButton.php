<?php
session_start(); // Start the session

// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "VotingSystem";

// Establish database connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check NominationApproval status for the logged-in user
$loggedInStudentID = isset($_SESSION['StudentID']) ? $_SESSION['StudentID'] : null;
$loggedInStudentEmail = isset($_SESSION['StudentEmail']) ? $_SESSION['StudentEmail'] : null;
$showEditButton = false;

if ($loggedInStudentID || $loggedInStudentEmail) {
    $sql = "SELECT NominationApproval FROM VSStudents WHERE StudentID = ? OR StudentEmail = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $loggedInStudentID, $loggedInStudentEmail);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($nominationApproval);
    $stmt->fetch();

    if ($nominationApproval == 1) {
        $showEditButton = true;
    }

    $stmt->close();
}

$conn->close();

// Display Edit SRC Details button if user has NominationApproval
if ($showEditButton) {
    echo '<a href="../Edit SRC Details/EditSRCDetailsPage.php">
            <button class="option-button-item Edit-SRC-Details-Button">
                <img src="../../../Images/Edit SRC Details Icon.png" width="45" height="45">
                EDIT SRC DETAILS
            </button>
        </a>';
}
?>
