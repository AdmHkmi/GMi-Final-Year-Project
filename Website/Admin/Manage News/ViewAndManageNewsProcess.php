<?php
session_start();

include '../../../Database/DatabaseConnection.php';

// Fetch news items
$fetch_news_sql = "SELECT NewsID, NewsTitle, NewsContent, NewsImage, IsActive FROM VSNews";
$news_result = $conn->query($fetch_news_sql);

// Display news items
if ($news_result->num_rows > 0) {
    echo "<div class='news-items'>";
    while ($news = $news_result->fetch_assoc()) {
        echo "<div class='news-item'>";
        echo "<h3>" . htmlspecialchars($news["NewsTitle"]) . "</h3>";
        echo "<div class='news-image'>";
        if (!empty($news["NewsImage"])) {
            echo "<img src='../../../News/" . htmlspecialchars($news["NewsImage"]) . "' alt='News Image'>";
        } else {
            echo "<span>No Image</span>";
        }
        echo "</div>";
        echo "<textarea readonly rows='4' style='resize: none; '>" . htmlspecialchars($news["NewsContent"]) . "</textarea>";
        echo "<p>Status: " . ($news["IsActive"] ? "Shared" : "Not Shared") . "</p>";
        echo "<div class='news-actions'>";
        echo "<form method='post' action='DeleteNewsProcess.php' onsubmit='return confirm(\"Are you sure you want to delete this news item?\");'>";
        echo "<input type='hidden' name='deleteNewsID' value='" . $news["NewsID"] . "'>";
        echo "<button type='submit'>Delete</button>";
        echo "</form>";
        echo "<button onclick='toggleEditRow(" . $news["NewsID"] . ")'>Edit</button>";
        echo "</div>";
        
// Edit form for each news item (hidden initially)
echo "<div id='edit-row-" . $news["NewsID"] . "' class='edit-news' style='display:none;'>";
echo "<form method='post' action='EditNewsProcess.php' enctype='multipart/form-data'>";
echo "<input type='hidden' name='newsID' value='" . $news["NewsID"] . "'>";
echo "<h4>Edit News</h4>";
echo "<div class='form-group'><label>Title:</label><input type='text' name='title' value='" . htmlspecialchars($news["NewsTitle"]) . "' required></div>";
echo "<div class='form-group'><label>Content:</label><textarea name='content' rows='5' required style='resize: none;'>" . htmlspecialchars($news["NewsContent"]) . "</textarea></div>";
echo "<div class='form-group'><label>Current Image:</label>";
if (!empty($news["NewsImage"])) {
    echo "<center><img src='../../../News/" . htmlspecialchars($news["NewsImage"]) . "' alt='News Image' style='width:200px; height:200px;'></center>";
} else {
    echo "<span>No Image</span>";
}
echo "</div>";
echo "<div class='form-group'><label>Change Image:</label><input type='file' name='image'></div>";
echo "<div class='form-group'><label>Status:</label>";
echo "<select name='is_active'>";
echo "<option value='1'" . ($news["IsActive"] ? " selected" : "") . ">Shared</option>";
echo "<option value='0'" . ($news["IsActive"] ? "" : " selected") . ">Not Shared</option>";
echo "</select>";
echo "</div>";
echo "<div class='form-group'><button type='submit' onclick=\"return confirm('Are you sure you want to save this changes?')\">Save Changes</button></div>";
echo "</form>";
echo "</div>";


        echo "</div>"; // Close news-item
    }
    echo "</div>"; // Close news-items
} else {
    echo "<p>No news items found.</p>";
}

$conn->close();
?>
