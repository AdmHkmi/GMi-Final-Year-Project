<?php
session_start();

// Check if the user is signed in
if (!isset($_SESSION['StudentID'])) {
    // Redirect to login page if not signed in
    echo "<script>
        alert('Session isn\\'t set up, please sign in first.');
        window.location.href = '../../../index.html'; // Adjust the path if necessary
    </script>";
    exit();
}

include '../../../Database/DatabaseConnection.php';

// Set PHP timezone to match your server's timezone
date_default_timezone_set('Asia/Kuala_Lumpur'); // Adjust as per your timezone

// Retrieve StudentID from URL parameter
$StudentID = $_GET['StudentID'];
$loggedInUser = $_SESSION['StudentID'];

// Get candidate details
$sql = "SELECT StudentID, StudentProfilePicture, StudentName, Manifesto FROM VSVote WHERE StudentID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $StudentID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $ViewStudentID = $row['StudentID'];
    $candidateName = $row['StudentName'];
    $profilePicture = $row['StudentProfilePicture'];
    $manifesto = $row['Manifesto'] ?: "The manifesto has not been announced yet.";
} else {
    $ViewStudentID = "Student ID Not Found";
    $candidateName = "Candidate Not Found";
    $profilePicture = ""; // Provide default or error image path
    $manifesto = "Manifesto not available.";
}

$stmt->close();

// Initialize variables for vote limit check
$hide_src_container = false;
$vote_message = "";

// Check SRCVoteLimit of the signed-in user
if ($loggedInUser) {
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
        $vote_message = "You have already cast all of your votes. Thank you for your participation.";
    }
}

// Check if the event is active
$event_sql = "SELECT * FROM VSEvents WHERE EventName = 'SRC Vote' AND NOW() BETWEEN StartDate AND EndDate";
$event_result = $conn->query($event_sql);
$event_active = ($event_result->num_rows > 0);

// Check if the user has already voted for this candidate
$voted = false;
$vote_check_sql = "SELECT CandidateID FROM VSVoteHistory WHERE VoterID = ? AND CandidateID = ? AND VoteType = 'SRC'";
$stmt_vote_check = $conn->prepare($vote_check_sql);
$stmt_vote_check->bind_param("ss", $loggedInUser, $StudentID);
$stmt_vote_check->execute();
$result_vote_check = $stmt_vote_check->get_result();

if ($result_vote_check->num_rows > 0) {
    $voted = true;
}

$stmt_vote_check->close();
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Candidate Details</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="ViewSRCDetails.css">
    <script>
        function confirmVote(CandidateID) {
            if (confirm("Are you sure you want to vote for this candidate?")) {
                document.getElementById('vote_form').submit();
            }
        }

        function disableVoteButton() {
            var voteButton = document.getElementById('vote_button');
            voteButton.disabled = true;
            voteButton.classList.add('disabled');
            voteButton.innerText = "Voted"; // Optional: Change button text to indicate it has been voted
        }

        // Disable the vote button if the candidate has already been voted for
        <?php if ($voted): ?>
        window.onload = function() {
            disableVoteButton();
        };
        <?php endif; ?>
    </script>
</head>
<body>
<div class="background-image"></div>
<header class="header">
    <div class="logo-container">
        <a href="../Home Page/UserHomepage.php">
            <img src="../../../Images/GMiLogo.png" class="GMiLogo" alt="GMi Logo">
        </a>
    </div>
    <div class="top-right-buttons">
        <a href="VoteCastingPage.php">
            <button class="back-button"><i class='fas fa-arrow-left'></i></button>
        </a>
    </div>
</header>
<div class="CandidateDetails">
<b><i>The details of <?php echo htmlspecialchars($candidateName); ?>.</i></b><br>
</div>
<main>
    <div class="candidate-details-container">
        <table>
            <tr><th>Profile Picture</th><th>Details</th></tr>
            <tr><td rowspan="7"><img src="../../../ProfilePicture/<?php echo htmlspecialchars($profilePicture); ?>" alt="Profile Picture" width='100' height='100'></td>
            <tr><td><b>Student ID</b></td></tr>
            <tr><td><?php echo htmlspecialchars($ViewStudentID); ?></td></tr>
            <tr><td><b>Name</b></td></tr>
            <tr><td><?php echo htmlspecialchars($candidateName); ?></td></tr>
            <tr><td><b>Manifesto</b></td></tr>
            <tr><td><?php echo htmlspecialchars($manifesto); ?></td></tr>  
            <tr>
                <td colspan="2">
                    <!-- Form for voting -->
                    <?php if ($event_active && !$hide_src_container && !$voted): ?>
                        <form id="vote_form" method="post" action="VoteCastingPage.php">
                            <input type="hidden" name="StudentID" value="<?php echo $StudentID; ?>">
                            <button id="vote_button" type="button" onclick="confirmVote('<?php echo $StudentID; ?>')" class="vote-button">Vote</button>
                        </form>
                    <?php else: ?>
                        <p>
                            <?php
                            if (!$event_active) {
                                echo "Voting is currently closed. Please check back later.";
                            } elseif ($voted) {
                                echo "You have already voted for this candidate.";
                            } elseif ($hide_src_container) {
                                echo $vote_message; // Show vote message if limit is reached
                            } else {
                                echo "You have reached the maximum vote limit.";
                            }
                            ?>
                        </p>
                    <?php endif; ?>
                </td>
            </tr>
        </table>
    </div>
</main>
<footer></footer>
</body>
</html>
