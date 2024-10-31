<?php
include '../../../Database/DatabaseConnection.php'; // Include database connection

// Initialize $loggedInUser from the session
$loggedInUser = isset($_SESSION['StudentID']) ? $_SESSION['StudentID'] : null;

// Initialize variables
$hide_src_container = false; // Initialize the variable
$vote_message = "";

// Check SRCVoteLimit of the signed-in user
if ($loggedInUser) {
    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Retrieve SRCVoteLimit for the logged-in user
    $stmt_src_status = $conn->prepare("SELECT SRCVoteLimit FROM VSVote WHERE StudentID = ?");
    $stmt_src_status->bind_param("s", $loggedInUser);
    $stmt_src_status->execute();
    $stmt_src_status->bind_result($SRCVoteLimit);
    $stmt_src_status->fetch();
    $stmt_src_status->close();

    // Check if SRCVoteLimit equals 2 to hide the SRC Container
    if ($SRCVoteLimit >= 2) {
        $hide_src_container = true;
        $vote_message = "You have already casted all of your votes. Thank you for your participation.";
    }

    $conn->close();
} else {
    $hide_src_container = true; // Optionally hide if user is not logged in
    $vote_message = "User session not properly initialized.";
}
?>
