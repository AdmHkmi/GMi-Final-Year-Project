<?php
session_start(); // Start the session
include '../../../Database/DatabaseConnection.php'; // Include database connection
include '../Home Page/CheckCandidateApproval.php';
include 'CheckEvent.php'; // Check for active event
include 'CheckVoteLimit.php'; // Check vote limit
include 'VoteProcess.php'; // Process voting logic

// Set PHP timezone to match your server's timezone
date_default_timezone_set('Asia/Kuala_Lumpur'); // Adjust as per your timezone

// Initialize variables
$vote_message = "";
$loggedInUser = isset($_SESSION['StudentID']) ? $_SESSION['StudentID'] : null;
$nomination_vote_limit = 3; // Set the nomination vote limit
$current_vote_count = 0;

// Check the user's current vote count
$conn_vote_check = new mysqli($servername, $username, $password, $dbname);
if ($conn_vote_check->connect_error) {
    die("Connection failed: " . $conn_vote_check->connect_error);
}

$sql_vote_count = "SELECT COUNT(*) as vote_count FROM VSVoteHistory WHERE VoterID = ? AND VoteType = 'Candidate'";
$stmt_vote_count = $conn_vote_check->prepare($sql_vote_count);
$stmt_vote_count->bind_param("s", $loggedInUser);
$stmt_vote_count->execute();
$result_vote_count = $stmt_vote_count->get_result();
if ($row = $result_vote_count->fetch_assoc()) {
    $current_vote_count = $row['vote_count'];
}
$stmt_vote_count->close();
$conn_vote_check->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
    <div class="logo-container">
        <a href="../Home Page/UserHomepage.php">
            <img src="../../../Images/GMiLogo.png" class="GMiLogo" alt="GMi Logo"> <!-- Logo -->
        </a>
    </div>
    <nav class="navbar">
        <ul>
            <li><a href="SRCNominationPage.php">SRC Nomination</a></li>
            <li><a href="../Vote Casting/VoteCastingPage.php">Vote Casting</a></li>
            <li><a href="../View Result/ViewResultPage.php">View Result</a></li>
            <li><a href="../GMi Updates/GMiUpdatesPage.php">GMi Updates</a></li>
            <?php if ($showEditButton): ?>
                <li><a href="../Edit SRC Details/EditSRCDetailsPage.php">Edit SRC Details</a></li>
            <?php endif; ?>
        </ul>
    </nav>
    <div class="top-right-buttons">
        <a href="../Home Page/UserHomepage.php">
            <button class="back-button"><i class='fas fa-arrow-left'></i></button> <!-- Back button -->
        </a>
    </div>
</header>

<main>
    <?php if ($event_active): ?>
        <?php if ($current_vote_count < $nomination_vote_limit): ?>
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
                        echo "<img src='../../../ProfilePicture/" . htmlspecialchars($row["StudentProfilePicture"]) . "' alt='Profile Picture'>";
                        echo "<h3 style='text-transform: uppercase;'>" . htmlspecialchars($row["StudentName"]) . "</h3>";
                        echo "<p>Student ID: " . htmlspecialchars($row["StudentID"]) . "</p>";
                        echo '<form id="form_' . $row["StudentID"] . '" method="post" action="' . htmlspecialchars($_SERVER["PHP_SELF"]) . '">';
                        echo '<input type="hidden" name="StudentID" value="' . $row["StudentID"] . '">';
                        echo '<button id="vote_button_' . $row["StudentID"] . '" type="button" onclick="confirmVote(\'' . $row["StudentID"] . '\')" class="vote-button">Vote</button>';
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
    <?php
    // PHP-generated JavaScript to disable buttons for already voted candidates
    $conn_disable = new mysqli($servername, $username, $password, $dbname);
    if ($conn_disable->connect_error) {
        die("Connection failed: " . $conn_disable->connect_error);
    }

    $voted_candidates_sql = "SELECT CandidateID FROM VSVoteHistory WHERE VoterID = ? AND VoteType = 'Candidate'";
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

        <?php else: ?>
            <!-- Include CandidateVoteHistory.php if the vote limit is reached -->
            <?php include 'ShowVoteHistory.php'; ?>
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
