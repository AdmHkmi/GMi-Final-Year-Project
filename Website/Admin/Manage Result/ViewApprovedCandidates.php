<?php
include '../../../Database/DatabaseConnection.php';


// Fetch approved SRC candidates
$sql = "SELECT StudentProfilePicture, StudentEmail, StudentID, StudentName FROM VSVote WHERE CandidateApproval = 1";
$result = $conn->query($sql);

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
    echo "<p>No user found.</p>";
}

$conn->close();
?>
