<?php
include '../../../Database/DatabaseConnection.php'; // Include the database connection

// Set PHP timezone to match your server's timezone
date_default_timezone_set('Asia/Kuala_Lumpur'); // Adjust as per your timezone

// Check if the logged-in user is set
$loggedInUser = isset($_SESSION['StudentID']) ? $_SESSION['StudentID'] : null;

if ($loggedInUser) {
    // Retrieve SRCVoteLimit for the logged-in user
    $stmt_src_status = $conn->prepare("SELECT CandidateVoteLimit FROM VSVote WHERE StudentID = ?");
    $stmt_src_status->bind_param("s", $loggedInUser);
    $stmt_src_status->execute();
    $stmt_src_status->bind_result($SRCVoteLimit);
    $stmt_src_status->fetch();
    $stmt_src_status->close();

    // Retrieve VoteLimit for the "Nomination Vote" event from VSEvents table
    $stmt_event_limit = $conn->prepare("SELECT VoteLimit FROM VSEvents WHERE EventName = 'Candidate Vote' AND IsActive = 1");
    $stmt_event_limit->execute();
    $stmt_event_limit->bind_result($EventVoteLimit);
    $stmt_event_limit->fetch();
    $stmt_event_limit->close();

    // Initialize hide_src_container and vote_message variables
    $hide_src_container = false;
    $vote_message = "";

    // Check if NominationVoteLimit exceeds or equals the event's VoteLimit
    if ($SRCVoteLimit >= $EventVoteLimit) {
        $hide_src_container = true;
        $vote_message = "You have already casted all of your votes. Thank you for your participation.";
    }

    // Process form submission for voting
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['StudentID'])) {
        $CandidateID = $_POST['StudentID'];

        // Begin transaction for atomicity
        $conn->begin_transaction();

        try {
            // Retrieve the candidate's name from VSVote
            $stmt_get_candidate_name = $conn->prepare("SELECT StudentName FROM VSVote WHERE StudentID = ?");
            $stmt_get_candidate_name->bind_param("s", $CandidateID);
            $stmt_get_candidate_name->execute();
            $stmt_get_candidate_name->bind_result($CandidateName);
            $stmt_get_candidate_name->fetch();
            $stmt_get_candidate_name->close();

            // Retrieve the voter's name from VSVote
            $stmt_get_voter_name = $conn->prepare("SELECT StudentName FROM VSVote WHERE StudentID = ?");
            $stmt_get_voter_name->bind_param("s", $loggedInUser);
            $stmt_get_voter_name->execute();
            $stmt_get_voter_name->bind_result($VoterName);
            $stmt_get_voter_name->fetch();
            $stmt_get_voter_name->close();

            // Proceed with the vote update
            $insert_vote_sql = "INSERT INTO VSVoteHistory (VoterID, CandidateID, CandidateName, VoterName, VoteType) VALUES (?, ?, ?, ?, ?)";
            $stmt_insert_vote = $conn->prepare($insert_vote_sql);
            $VoteType = "SRC"; // Set VoteType to "Candidate"
            $stmt_insert_vote->bind_param("sssss", $loggedInUser, $CandidateID, $CandidateName, $VoterName, $VoteType);
            $stmt_insert_vote->execute();
            $stmt_insert_vote->close();

            // Increment CandidateVoteLimit for the logged-in user
            $increment_status_sql = "UPDATE VSVote SET CandidateVoteLimit = CandidateVoteLimit + 1 WHERE StudentID = ?";
            $stmt_increment_status = $conn->prepare($increment_status_sql);
            $stmt_increment_status->bind_param("s", $loggedInUser);
            $stmt_increment_status->execute();
            $stmt_increment_status->close();

            // Update TotalSRCVote for the candidate in VSVote table
            $update_total_vote_sql = "UPDATE VSVote SET TotalSRCVote = TotalSRCVote + 1 WHERE StudentID = ?";
            $stmt_update_total_vote = $conn->prepare($update_total_vote_sql);
            $stmt_update_total_vote->bind_param("s", $CandidateID);
            $stmt_update_total_vote->execute();
            $stmt_update_total_vote->close();

            // Commit the transaction
            $conn->commit();

            // Update session variable with incremented CandidateVoteLimit
            $_SESSION['CandidateVoteLimit'] = isset($_SESSION['CandidateVoteLimit']) ? $_SESSION['CandidateVoteLimit'] + 1 : 1;

            // Check if NominationVoteLimit has reached or exceeded the VoteLimit from VSEvents table
            if ($_SESSION['CandidateVoteLimit'] >= $EventVoteLimit) {
                $hide_src_container = true;
                $vote_message = "You have already casted all of your votes. Thank you for your participation.";
            } else {
                // Reset vote message if not all votes are casted
                $vote_message = "";
            }

        } catch (Exception $e) {
            // Rollback the transaction on error
            $conn->rollback();
            $vote_message = "Transaction failed: " . $e->getMessage();
        }
    }
} else {
    $vote_message = "User session not properly initialized.";
}
$conn->close();
?>
