<?php
session_start(); // Start the session to access session variables

if (!isset($_SESSION['StudentID'])) {
    // Use JavaScript to show an alert and redirect
    echo '<script>alert("Session is not set up, please sign in first."); window.location.href = "../../../index.html";</script>';
    exit; // Ensure no further code is executed
}

include "../Home Page/CheckCandidateApproval.php"; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GMi Updates Page</title>
    <link rel="icon" type="image/icon" href="../../../Images/favicon.ico">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="GMiUpdatesPage.css">
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
                <li><a href="../SRC Nomination/SRCNominationPage.php">SRC Nomination</a></li>
                <li><a href="../Vote Casting/VoteCastingPage.php">Vote Casting</a></li>
                <li><a href="../View Result/ViewResultPage.php">View Result</a></li>
                <li><a href="GMiUpdatesPage.php">GMi Updates</a></li>
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
        <?php
        include '../../../Database/DatabaseConnection.php';

        // Set PHP timezone to match your server's timezone
        date_default_timezone_set('Asia/Kuala_Lumpur'); // Adjust as per your timezone

        // Initialize variables
        $vote_message = "";
        $loggedInStudentID = isset($_SESSION['StudentID']) ? $_SESSION['StudentID'] : null;

        // Check for an active event named "Nomination Vote"
        $event_active = false;

        // Fetch events
        $fetch_events_sql = "SELECT EventID, EventName, StartDate, EndDate, IsActive FROM VSEvents";
        $events_result = $conn->query($fetch_events_sql);

        // Display events
        echo "<div class='Event-Container'>";
        echo "<div class='text'><h2>Events</h2></div>";
        if ($events_result->num_rows > 0) {
            echo "<table>";
            echo "<tr><th>Event Name</th><th>Start Date</th><th>End Date</th></tr>";
            while ($event = $events_result->fetch_assoc()) {
                if ($event["EventName"] === "Nomination Result" || $event["EventName"] === "SRC Result") {
                    continue; // Skip displaying "Voting Result" event
                }
                
                $startDateDisplay = is_null($event["StartDate"]) ? "To Be Announced" : $event["StartDate"];
                $endDateDisplay = is_null($event["EndDate"]) ? "To Be Announced" : $event["EndDate"];
                
                echo "<tr>";
                echo "<td>" . htmlspecialchars($event["EventName"]) . "</td>";
                echo "<td>" . $startDateDisplay . "</td>";
                echo "<td>" . $endDateDisplay . "</td>";
                echo "</tr>";
            }
            echo "</table>";
            echo "</div>";
        } else {
            echo "<div class='Message-Container'>";
            echo "<p>There's no event, please check back later!</p>";
            echo "</div>";
        }

        // Fetch news items
        $fetch_news_sql = "SELECT NewsID, NewsTitle, NewsContent, NewsImage, IsActive FROM VSNews WHERE IsActive = 1";
        $news_result = $conn->query($fetch_news_sql);

        // Display news items
        echo "<div class='News-Container'>";
        echo "<h2>News</h2>";
        if ($news_result->num_rows > 0) {
            echo "<div class='news-grid'>";
            while ($news = $news_result->fetch_assoc()) {
                echo "<div class='news-item'>";
                echo "<h3>" . htmlspecialchars($news["NewsTitle"]) . "</h3>";
                echo "<br>";
                if (!empty($news["NewsImage"])) {
                    echo "<img src='../../../News/" . htmlspecialchars($news["NewsImage"]) . "' alt='News Image'>";
                }
                echo "<br>";
                echo "<textarea class='news-content' readonly>" . htmlspecialchars($news["NewsContent"]) . "</textarea>";
                echo "</div>";
            }      
            echo "</div>";
        } else {
            echo "<div class='Message-Container'>";
            echo "<p>There's no news, please check back later!</p>";
            echo "</div>";
        }

        $conn->close();
        ?>
    </main>
    <footer></footer>
</body>
</html>
