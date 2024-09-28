<?php
session_start(); // Start the session
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
echo "<h2>Events</h2>";
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
