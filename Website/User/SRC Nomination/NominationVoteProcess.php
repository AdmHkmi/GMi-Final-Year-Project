<?php
include '../../../Database/DatabaseConnection.php'; // Include the database connection

// Set PHP timezone to match your server's timezone
date_default_timezone_set('Asia/Kuala_Lumpur'); // Adjust as per your timezone

// Check if the logged-in user is set
$loggedInUser = isset($_SESSION['StudentID']) ? $_SESSION['StudentID'] : null;

if ($loggedInUser) {
    // Retrieve NominationVoteLimit for the logged-in user
    $stmt_src_status = $conn->prepare("SELECT NominationVoteLimit FROM VSVote WHERE StudentID = ?");
    $stmt_src_status->bind_param("s", $loggedInUser);
    $stmt_src_status->execute();
    $stmt_src_status->bind_result($NominationVoteLimit);
    $stmt_src_status->fetch();
    $stmt_src_status->close();

    // Check if NominationVoteLimit equals 3 to hide the SRC Container
    $hide_src_container = false;
    $vote_message = "";

    if ($NominationVoteLimit >= 3) {
        $hide_src_container = true;
        $vote_message = "You have already casted all of your votes. Thank you for your participation.";
    }

    // Process form submission for voting
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['StudentID'])) {
        $CandidateID = $_POST['StudentID'];

        // Check if the signed-in user has already voted for this candidate
        $check_vote_sql = "SELECT COUNT(*) AS count FROM VSVoteHistory WHERE VoterID = ? AND CandidateID = ?";
        $stmt_check_vote = $conn->prepare($check_vote_sql);
        $stmt_check_vote->bind_param("ss", $loggedInUser, $CandidateID);
        $stmt_check_vote->execute();
        $result_check_vote = $stmt_check_vote->get_result();
        $row_check_vote = $result_check_vote->fetch_assoc();

        if ($row_check_vote['count'] > 0) {
            $vote_message = "You have already voted for this candidate. You cannot vote again.";
        } else {
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
                $insert_vote_sql = "INSERT INTO VSVoteHistory (VoterID, CandidateID, CandidateName, VoterName) VALUES (?, ?, ?, ?)";
                $stmt_insert_vote = $conn->prepare($insert_vote_sql);
                $stmt_insert_vote->bind_param("ssss", $loggedInUser, $CandidateID, $CandidateName, $VoterName);
                $stmt_insert_vote->execute();
                $stmt_insert_vote->close();

                // Increment NominationVoteLimit for the logged-in user
                $increment_status_sql = "UPDATE VSVote SET NominationVoteLimit = NominationVoteLimit + 1 WHERE StudentID = ?";
                $stmt_increment_status = $conn->prepare($increment_status_sql);
                $stmt_increment_status->bind_param("s", $loggedInUser);
                $stmt_increment_status->execute();
                $stmt_increment_status->close();

                // Update TotalCandidateVote for the candidate in VSVote table
                $update_total_vote_sql = "UPDATE VSVote SET TotalCandidateVote = TotalCandidateVote + 1 WHERE StudentID = ?";
                $stmt_update_total_vote = $conn->prepare($update_total_vote_sql);
                $stmt_update_total_vote->bind_param("s", $CandidateID);
                $stmt_update_total_vote->execute();
                $stmt_update_total_vote->close();

                // Commit the transaction
                $conn->commit();

                // Update session variable with incremented NominationVoteLimit
                $_SESSION['NominationVoteLimit'] = isset($_SESSION['NominationVoteLimit']) ? $_SESSION['NominationVoteLimit'] + 1 : 1;

                // Check if NominationVoteLimit has reached 3 to hide the SRC Container
                if ($_SESSION['NominationVoteLimit'] >= 3) {
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

        $stmt_check_vote->close();
    }
} else {
    $vote_message = "User session not properly initialized.";
}

// Close connection if necessary at the end of the script
// Note: Only close connection if you are done with all operations
// $conn->close();
?>
