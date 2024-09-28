<?php
session_start(); // Start the session
include '../../../Database/DatabaseConnection.php';

// Set PHP timezone to match your server's timezone
date_default_timezone_set('Asia/Kuala_Lumpur'); // Adjust as per your timezone

// Initialize variables
$vote_message = "";
$loggedInUser = isset($_SESSION['StudentID']) ? $_SESSION['StudentID'] : null;

// Check for an active event named "Nomination Vote"
$event_active = false;

$current_date = date('Y-m-d H:i:s');
$check_event_sql = "SELECT EventID FROM VSEvents WHERE EventName = 'Nomination Vote' AND IsActive = 1 AND StartDate <= ? AND EndDate >= ?";
$stmt_check_event = $conn->prepare($check_event_sql); // Use $conn instead of $conn_event
$stmt_check_event->bind_param("ss", $current_date, $current_date);
$stmt_check_event->execute();
$result_event = $stmt_check_event->get_result();

if ($result_event->num_rows > 0) {
    $event_active = true;
}

$stmt_check_event->close();

// Check NominationVoteLimit of the signed-in user
$hide_src_container = false; // Initialize the variable

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
}

// Process form submission for voting
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['StudentID']) && $event_active) {
    $CandidateID = $_POST['StudentID'];

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check if the signed-in user has already voted for this candidate
    if ($loggedInUser) {
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

                // Retrieve the voter's name from VSVote (or any appropriate table)
                $stmt_get_voter_name = $conn->prepare("SELECT StudentName FROM VSVote WHERE StudentID = ?");
                $stmt_get_voter_name->bind_param("s", $loggedInUser);
                $stmt_get_voter_name->execute();
                $stmt_get_voter_name->bind_result($VoterName);
                $stmt_get_voter_name->fetch();
                $stmt_get_voter_name->close();

                // Proceed with the vote update, including the CandidateName and VoterName
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

            // Close statements
            $stmt_check_vote->close();
        }

    } else {
        $vote_message = "User session not properly initialized.";
    }

    // Close connection
    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Vote Casting Page</title>
    <link rel="stylesheet" href="SRCNominationPage.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script>
        function confirmVote(CandidateID) {
            if (confirm("Are you sure you want to vote for this candidate?")) {
                document.getElementById('form_' + CandidateID).submit();
            }
        }
        
        function viewCandidateDetails(StudentID) {
            window.location.href = 'ViewSRCDetails.php?StudentID=' + StudentID;
        }

        // Function to disable vote button after voting
        function disableVoteButton(CandidateID) {
            var voteButton = document.getElementById('vote_button_' + CandidateID);
            voteButton.disabled = true;
            voteButton.classList.add('disabled');
        }
    </script>
</head>
<body>
<div class="background-image"></div>
<header class="header">
    <div class="logo-container"><a href="../Home Page/UserHomepage.php"><img src="../../../Images/GMiLogo.png" class="GMiLogo" alt="GMi Logo"></a></div>
    <div class="top-right-buttons"><a href="../Home Page/UserHomepage.php"><button class="back-button"><i class='fas fa-arrow-left' ></i></button></a></div>
</header>
<main>
    <?php if ($event_active): ?>
        <?php if (!$hide_src_container): ?>
            <div class="Instruction">
            <b><i>Please vote for the candidate you want.</i></b><br>
            <b><i>You can only vote thrice.</i></b>
            </div>
            <div class="Search">
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <label for="search">Search:</label>
                <input type="text" id="search" name="search" placeholder="Enter Name or StudentID">
                <button type="submit">Search</button>
            </form>
            <?php if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search'])) { include 'SearchParticipant.php'; } ?>
            </div>
            <div class="SRC-Container">
                <?php
                // Create connection for displaying candidates
                $conn_display = new mysqli($servername, $username, $password, $dbname);
                if ($conn_display->connect_error) {
                    die("Connection failed: " . $conn_display->connect_error);
                }
                
                $sql = "SELECT StudentID, StudentProfilePicture, StudentName FROM VSStudents WHERE UserApproval = 1";
                $result = $conn_display->query($sql);
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<div class='candidate-card'>";
                        echo "<img src='../../../ProfilePicture/". htmlspecialchars($row["StudentProfilePicture"])."' alt='Profile Picture'>";
                        echo "<h3 style='text-transform: uppercase;'>".htmlspecialchars($row["StudentName"])."</h3>";
                        echo "<p>Student ID: ".htmlspecialchars($row["StudentID"])."</p>";
                        echo '<form id="form_'.$row["StudentID"].'" method="post" action="'.htmlspecialchars($_SERVER["PHP_SELF"]).'">';
                        echo '<input type="hidden" name="StudentID" value="'.$row["StudentID"].'">';
                        echo '<button id="vote_button_'.$row["StudentID"].'" type="button" onclick="confirmVote(\''.$row["StudentID"].'\')" class="vote-button">Vote</button>';
                        echo '</form>';
                        echo "</div>";
                    }
                } else {
                    echo "<p>No candidates available</p>";
                }
                $conn_display->close();
                ?>
            </div>
            <script>
                // PHP-generated JavaScript to disable buttons for already voted candidates
                <?php
                $conn_disable = new mysqli($servername, $username, $password, $dbname);
                if ($conn_disable->connect_error) {
                    die("Connection failed: " . $conn_disable->connect_error);
                }

                // Query to get list of candidates voted by the user
                $voted_candidates_sql = "SELECT CandidateID FROM VSVoteHistory WHERE StudentID = ?";
                $stmt_voted_candidates = $conn_disable->prepare($voted_candidates_sql);
                $stmt_voted_candidates->bind_param("s", $loggedInUser);
                $stmt_voted_candidates->execute();
                $result_voted_candidates = $stmt_voted_candidates->get_result();

                while ($row_voted_candidates = $result_voted_candidates->fetch_assoc()) {
                    $voted_StudentID = $row_voted_candidates['CandidateID'];
                    echo "disableVoteButton('$voted_StudentID');";
                }

                $stmt_voted_candidates->close();
                $conn_disable->close();
                ?>
            </script>
        <?php endif; ?>
    <?php else: ?>
        <div class="Message-Container">
        <p>There is no active voting event at the moment. <br><br>Please check on the <a href="../GMi Updates/GMiUpdatesPage.php">GMiUpdates</a> page.</p>
        </div>
    <?php endif; ?>
    <?php if (!empty($vote_message)): ?>
        <div class="Message-Container">
        <p><?php echo $vote_message; ?></p>
        </div>
    <?php endif; ?>
</main>
<footer>
</footer>
</body>
</html>