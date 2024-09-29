<?php
include '../../../Database/DatabaseConnection.php'; // Include database connection

// Initialize $loggedInUser from the session
$loggedInUser = isset($_SESSION['StudentID']) ? $_SESSION['StudentID'] : null;

// Initialize variables
$hide_src_container = false; // Initialize the variable
$vote_message = "";

// Check NominationVoteLimit of the signed-in user
if ($loggedInUser) {
    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Retrieve NominationVoteLimit for the logged-in user
    $stmt_src_status = $conn->prepare("SELECT NominationVoteLimit FROM VSVote WHERE StudentID = ?");
    $stmt_src_status->bind_param("s", $loggedInUser);
    $stmt_src_status->execute();
    $stmt_src_status->bind_result($NominationVoteLimit);
    $stmt_src_status->fetch();
    $stmt_src_status->close();

    // Check if NominationVoteLimit equals 3 to hide the SRC Container
    if ($NominationVoteLimit >= 3) {
        $hide_src_container = true;
        $vote_message = "You have already casted all of your votes. Thank you for your participation.";
    }

    $conn->close();
} else {
    $hide_src_container = true; // Optionally hide if user is not logged in
    $vote_message = "User session not properly initialized.";
}
?>
