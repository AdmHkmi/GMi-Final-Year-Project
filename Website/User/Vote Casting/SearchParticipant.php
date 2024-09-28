<?php
// Start session if it's not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
include '../../../Database/DatabaseConnection.php';

// Initialize variables
$search_query = isset($_POST['search']) ? $_POST['search'] : '';
$search = $search_query; // Store the original search query for display

// Prepare the SQL query for searching candidates
$search_query = '%' . $conn->real_escape_string($search_query) . '%';
$sql = "SELECT s.StudentID, s.StudentProfilePicture, s.StudentName FROM VSStudents s JOIN VSVote v ON s.StudentID = v.StudentID WHERE (s.StudentName LIKE ? OR s.StudentID LIKE ?) AND s.UserApproval = 1 AND v.CandidateApproval = 1";

// Prepare and bind the statement
$stmt = $conn->prepare($sql);
$stmt->bind_param('ss', $search_query, $search_query);

// Execute the statement
$stmt->execute();
$result = $stmt->get_result();

// Display search results message
echo "<br><center>Search results for '" . htmlspecialchars($search) . "'</center>";

// Check if there are results
if ($result->num_rows > 0) {
    echo "<div class='SRC-Container'>";
    while ($row = $result->fetch_assoc()) {
        echo "<div class='candidate-card'>";
        echo "<img src='../../../ProfilePicture/" . htmlspecialchars($row["StudentProfilePicture"]) . "' alt='Profile Picture'>";
        echo "<h3>" . htmlspecialchars($row["StudentName"]) . "</h3>";
        echo "<p>Student ID: " . htmlspecialchars($row["StudentID"]) . "</p>";
        echo '<form id="form_' . $row["StudentID"] . '" method="post" action="' . htmlspecialchars($_SERVER["PHP_SELF"]) . '">';
        echo '<input type="hidden" name="CandidateID" value="' . $row["StudentID"] . '">';
        echo '<button style="margin: 10px; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; width: calc(100% - 40px); background-color: #24287E; color: #D9D9D9;" id="vote_button_' . $row["StudentID"] . '" type="button" onclick="confirmVote(\'' . $row["StudentID"] . '\')" class="vote-button">Vote</button>';
        echo '<button style="margin: 10px; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; width: calc(100% - 40px); background-color: #D9D9D9; color: #24287E;" type="button" onclick="viewCandidateDetails(\'' . $row["StudentID"] . '\')" class="view-manifesto-button">Manifesto</button>';
        echo '</form>';
        echo "</div>";
    }
    echo "</div>";
} else {
    echo "<p>No candidates found for the search query.</p>";
}

// Close statements and connection
$stmt->close();
$conn->close();
?>
