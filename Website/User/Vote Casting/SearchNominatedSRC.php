<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start the session only if it's not already started
}
include '../../../Database/DatabaseConnection.php';

// Initialize variables
$search_query = isset($_POST['search']) ? $_POST['search'] : '';

// Prepare the SQL query for searching candidates
$search_query = '%' . $conn->real_escape_string($search_query) . '%';
$sql = "SELECT StudentID, StudentProfilePicture, StudentName 
        FROM VSStudents 
        WHERE (StudentName LIKE ? OR StudentID LIKE ?) 
          AND UserApproval = 1 
          AND NominationApproval = 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ss', $search_query, $search_query);
$stmt->execute();
$result = $stmt->get_result();

// Check if there are results
if ($result->num_rows > 0) {
    echo "<div class='SRC-Container'>";
    while ($row = $result->fetch_assoc()) {
        echo "<div class='candidate-card'>";
        echo "<img src='../../../ProfilePicture/". htmlspecialchars($row["StudentProfilePicture"])."' alt='Profile Picture'>";
        echo "<h3>".htmlspecialchars($row["StudentName"])."</h3>";
        echo "<p>Student ID: ".htmlspecialchars($row["StudentID"])."</p>";
        echo '<form id="form_'.$row["StudentID"].'" method="post" action="'.htmlspecialchars($_SERVER["PHP_SELF"]).'">';
        echo '<input type="hidden" name="CandidateID" value="'.$row["StudentID"].'">';
        echo '<button style="margin: 10px; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; width: calc(100% - 40px); background-color: #24287E; color: #D9D9D9;" id="vote_button_'.$row["StudentID"].'" type="button" onclick="confirmVote(\''.$row["StudentID"].'\')" class="vote-button">Vote</button>';
        echo '<button style="margin: 10px; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; width: calc(100% - 40px); background-color: #D9D9D9; color: #24287E;" type="button" onclick="viewCandidateDetails(\''.$row["StudentID"].'\')" class="view-manifesto-button">Manifesto</button>';
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
