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
$search_query = "";

if (!isset($_SESSION['StudentID'])) {
    // Use JavaScript to show an alert and redirect
    echo '<script>alert("Session is not set up, please sign in first."); window.location.href = "../../../index.html";</script>';
    exit; // Ensure no further code is executed
}

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

// Handle search request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search'])) {
    $search_query = trim($_POST['search']);
}
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
            var voteButtons = document.querySelectorAll('.vote-button[data-id="' + CandidateID + '"]');
            voteButtons.forEach(button => {
                button.disabled = true;
                button.classList.add('disabled');
            });
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
                    <input type="text" id="search" name="search" placeholder="Enter Name or StudentID" value="<?php echo htmlspecialchars($search_query); ?>">
                    <button type="submit">Search</button>
                </form>
            </div>
            <div class="SRC-Container">
                <?php
                // Create connection for displaying candidates
                $conn_display = new mysqli($servername, $username, $password, $dbname);
                if ($conn_display->connect_error) {
                    die("Connection failed: " . $conn_display->connect_error);
                }

                $sql = "SELECT StudentID, StudentProfilePicture, StudentName FROM VSStudents WHERE UserApproval = 1";
                if (!empty($search_query)) {
                    $sql .= " AND (StudentName LIKE ? OR StudentID LIKE ?)";
                }
                
                $stmt = $conn_display->prepare($sql);
                if (!empty($search_query)) {
                    $search_term = '%' . $search_query . '%';
                    $stmt->bind_param("ss", $search_term, $search_term);
                }
                $stmt->execute();
                $result = $stmt->get_result();

                $voted_candidates = [];
                $voted_candidates_sql = "SELECT CandidateID FROM VSVoteHistory WHERE VoterID = ? AND VoteType = 'Candidate'";
                $stmt_voted_candidates = $conn_display->prepare($voted_candidates_sql);
                $stmt_voted_candidates->bind_param("s", $loggedInUser);
                $stmt_voted_candidates->execute();
                $result_voted_candidates = $stmt_voted_candidates->get_result();
                while ($row = $result_voted_candidates->fetch_assoc()) {
                    $voted_candidates[] = $row['CandidateID'];
                }

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $disabled_class = in_array($row["StudentID"], $voted_candidates) ? 'disabled' : '';
                        $button_disabled = in_array($row["StudentID"], $voted_candidates) ? 'disabled' : '';
                        echo "<div class='candidate-card'>";
                        echo "<img src='../../../ProfilePicture/" . htmlspecialchars($row["StudentProfilePicture"]) . "' alt='Profile Picture'>";
                        echo "<h3 style='text-transform: uppercase;'>" . htmlspecialchars($row["StudentName"]) . "</h3>";
                        echo "<p>Student ID: " . htmlspecialchars($row["StudentID"]) . "</p>";
                        echo '<form id="form_' . $row["StudentID"] . '" method="post" action="' . htmlspecialchars($_SERVER["PHP_SELF"]) . '">';
                        echo '<input type="hidden" name="StudentID" value="' . $row["StudentID"] . '">';
                        echo '<button id="vote_button_' . $row["StudentID"] . '" class="vote-button ' . $disabled_class . '" data-id="' . $row["StudentID"] . '" type="button" onclick="confirmVote(\'' . $row["StudentID"] . '\')" ' . $button_disabled . '>Vote</button>';
                        echo '</form>';
                        echo "</div>";
                    }
                } else {
                    echo "<p>No candidates available</p>";
                }
                $stmt_voted_candidates->close();
                $conn_display->close();
                ?>
            </div>
            <script>
                <?php
                foreach ($voted_candidates as $candidateID) {
                    echo "disableVoteButton('$candidateID');";
                }
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
