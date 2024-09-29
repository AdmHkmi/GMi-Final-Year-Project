<?php
include '../../../Database/DatabaseConnection.php';

// Get search input from the form
$search = isset($_POST['search']) ? $conn->real_escape_string($_POST['search']) : '';

// Prepare SQL query to search SRC
$sql = "SELECT StudentProfilePicture, StudentName, StudentEmail, StudentID
        FROM VSVote
        WHERE (StudentName LIKE ? OR StudentID LIKE ?) AND SRCApproval = 1";
$stmt = $conn->prepare($sql);

// Bind parameters
$searchTerm = "%" . $search . "%";
$stmt->bind_param("ss", $searchTerm, $searchTerm);

// Execute the query
$stmt->execute();
$result = $stmt->get_result();

// Display search results
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<div class='Candidate-Card'>";
        echo "<img src='../../../ProfilePicture/". htmlspecialchars($row["StudentProfilePicture"]) ."' alt='Profile Picture'>";
        echo "<div class='candidate-info'>";
        echo "<h3>". htmlspecialchars($row["StudentName"]) ."</h3>";
        echo "<p>Student ID: ". htmlspecialchars($row["StudentID"]) ."</p>";
        echo "<p>Email: ". htmlspecialchars($row["StudentEmail"]) ."</p>";
        echo "</div>";
        echo "</div>";
    }
} else {
    echo "<p>No results found for your search.</p>";
}

// Close connection
$conn->close();
?>
