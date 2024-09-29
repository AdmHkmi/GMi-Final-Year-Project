<?php
session_start(); // Start the session
include '../../../Database/DatabaseConnection.php';

// Set PHP timezone to match your server's timezone
date_default_timezone_set('Asia/Kuala_Lumpur'); // Adjust as per your timezone

// Initialize variables
$nomination_message = "";
$src_message = "";
$nomination_active = false;
$src_active = false;
$nomination_isActive = false;
$src_isActive = false;
$nomination_name = "";
$src_name = "";

// Check for an active event named "Nomination Result"
$conn_nomination = new mysqli($servername, $username, $password, $dbname);
if ($conn_nomination->connect_error) {
    die("Connection failed: " . $conn_nomination->connect_error);
}

$check_nomination_sql = "SELECT EventID, EventName, IsActive FROM VSEvents WHERE EventName = 'Nomination Result'";
$result_nomination = $conn_nomination->query($check_nomination_sql);

if ($result_nomination->num_rows > 0) {
    $nomination_data = $result_nomination->fetch_assoc();
    $nomination_active = true;
    $nomination_isActive = (bool) $nomination_data['IsActive']; // Convert to boolean
    $nomination_name = $nomination_data['EventName'];
}

$conn_nomination->close();

// Check for an active event named "SRC Result"
$conn_src = new mysqli($servername, $username, $password, $dbname);
if ($conn_src->connect_error) {
    die("Connection failed: " . $conn_src->connect_error);
}

$check_src_sql = "SELECT EventID, EventName, IsActive FROM VSEvents WHERE EventName = 'SRC Result'";
$result_src = $conn_src->query($check_src_sql);

if ($result_src->num_rows > 0) {
    $src_data = $result_src->fetch_assoc();
    $src_active = true;
    $src_isActive = (bool) $src_data['IsActive']; // Convert to boolean
    $src_name = $src_data['EventName'];
}

$conn_src->close();

// Message for displaying results or informing about the event status - Nomination Result
if ($nomination_active && $nomination_name === "Nomination Result" && $nomination_isActive) {
    // Since we want to remove the specific message, we don't set $nomination_message here
} else {
    $nomination_message = "The results haven't been published yet. Please try again later.";
}

// Message for displaying results or informing about the event status - SRC Result
if ($src_active && $src_name === "SRC Result" && $src_isActive) {
    // Since we want to remove the specific message, we don't set $src_message here
} else {
    $src_message = "The results haven't been published yet. Please try again later.";
}

// Check if both results are not active
$show_default_message = !$nomination_isActive && !$src_isActive;
?>

<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vote Casting Page</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="ViewResultPage.css">
</head>
<body>
<div class="background-image"></div>
<header class="header">
    <div class="logo-container"><a href="../Home Page/UserHomepage.php"><img src="../../../Images/GMiLogo.png" class="GMiLogo" alt="GMi Logo"></a></div>
    <div class="top-right-buttons"><a href="../Home Page/UserHomepage.php"><button class="back-button"><i class='fas fa-arrow-left'></i></button></a></div>
</header>
<main>
    <?php if ($nomination_active && $nomination_name === "Nomination Result"): ?>
        <?php if ($nomination_isActive): ?>
            <h2>Nomination Results</h2>
            <p>Thank you to everyone who participated in the nomination process.</p>
            <div class="Result-Container">
                <?php
                // Create connection for displaying candidates
                $conn_nomination_display = new mysqli($servername, $username, $password, $dbname);
                if ($conn_nomination_display->connect_error) {
                    die("Connection failed: " . $conn_nomination_display->connect_error);
                }
                
                $sql_nomination = "SELECT StudentProfilePicture, StudentName, StudentID FROM VSVote WHERE CandidateApproval = 1";
                $result_nomination = $conn_nomination_display->query($sql_nomination);
                if ($result_nomination->num_rows > 0) {
                    while ($row = $result_nomination->fetch_assoc()) {
                        echo "<div class='Candidate-Card'>";
                        echo "<img src='../../../ProfilePicture/". $row["StudentProfilePicture"]."' alt='Profile Picture'>";
                        echo "<div class='candidate-info'>";
                        echo "<h3>".htmlspecialchars($row["StudentName"])."</h3>";
                        echo "<p>Student ID: ".htmlspecialchars($row["StudentID"])."</p>";
                        echo "</div>";
                        echo "</div>";
                    }
                } else {
                    echo "<div class='Message-Container'>";
                    echo "<p>No candidates available</p>";
                    echo "</div>";
                }
                $conn_nomination_display->close();
                ?>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="Message-Container">
            <p><?php echo $nomination_message; ?></p>
        </div>
    <?php endif; ?>

    <?php if ($src_active && $src_name === "SRC Result"): ?>
        <?php if ($src_isActive): ?>
            <h2>Introducing our new SRC (Student Representative Council)!</h2>
            <p>Thanks to everyone who participated in this voting event.</p>
            <div class="Result-Container">
                <?php
                // Create connection for displaying SRC candidates
                $conn_src_display = new mysqli($servername, $username, $password, $dbname);
                if ($conn_src_display->connect_error) {
                    die("Connection failed: " . $conn_src_display->connect_error);
                }
                
                $sql_src = "SELECT StudentProfilePicture, StudentName, StudentID FROM VSVote WHERE SRCApproval = 1";
                $result_src = $conn_src_display->query($sql_src);
                if ($result_src->num_rows > 0) {
                    while ($row = $result_src->fetch_assoc()) {
                        echo "<div class='Candidate-Card'>";
                        echo "<img src='../../../ProfilePicture/". $row["StudentProfilePicture"]."' alt='Profile Picture'>";
                        echo "<div class='candidate-info'>";
                        echo "<h3>".htmlspecialchars($row["StudentName"])."</h3>";
                        echo "<p>Student ID: ".htmlspecialchars($row["StudentID"])."</p>";
                        echo "</div>";
                        echo "</div>";
                    }
                } else {
                    echo "<div class='Message-Container'>";
                    echo "<p>No candidates available</p>";
                    echo "</div>";
                }
                $conn_src_display->close();
                ?>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="Message-Container">
            <p><?php echo $src_message; ?></p>
        </div>
    <?php endif; ?>

    <?php if ($show_default_message): ?>
        <div class="Message-Container">
            <p>The results haven't been published yet. Please try again later.</p>
        </div>
    <?php endif; ?>

    <div class="vote-message"></div>
</main>
<footer></footer>
</body>
</html>
