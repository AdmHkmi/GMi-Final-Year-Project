<?php
include '../../../Database/DatabaseConnection.php';

// Query to get all candidates and SRC data
$sql = "SELECT StudentProfilePicture, StudentName, StudentEmail, StudentID, 
                TotalCandidateVote, 
                (SELECT COUNT(*) FROM VSCurrentCandidate WHERE StudentID = VSStudents.StudentID) AS CandidateStatus,
                TotalSRCVote,
                (SELECT COUNT(*) FROM VSCurrentSRC WHERE StudentID = VSStudents.StudentID) AS SRCStatus
        FROM VSStudents 
        WHERE UserApproval = 1
        ORDER BY TotalCandidateVote DESC, TotalSRCVote DESC";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<div class='participants-grid'>"; // Create a grid container for participants
    while ($row = $result->fetch_assoc()) {
        echo "<div class='participant-card'>"; // Start participant card

        // Display profile picture
        echo "<img src='../../../ProfilePicture/" . $row["StudentProfilePicture"] . "' alt='Profile Picture' class='profile-picture'>";

        // Display name and other details
        echo "<div class='participant-info'>";
        echo "<h3>" . htmlspecialchars($row["StudentName"]) . "</h3>";
        echo "<p>Email: " . htmlspecialchars($row["StudentEmail"]) . "</p>";
        echo "<p>Student ID: " . htmlspecialchars($row["StudentID"]) . "</p>";
        echo "<p>Total Candidate Votes: " . htmlspecialchars($row["TotalCandidateVote"]) . "</p>";
        echo "<p>Total SRC Votes: " . htmlspecialchars($row["TotalSRCVote"]) . "</p>";

        // Candidate Status
        echo "<p>Candidate Status: ";
        if ($row["CandidateStatus"] > 0) {
            echo "<span class='active'>Candidate Active</span>";
        } else {
            echo "<span class='inactive'>Candidate Inactive</span>";
        }
        echo "</p>";

        // SRC Status
        echo "<p>SRC Status: ";
        if ($row["SRCStatus"] > 0) {
            echo "<span class='active'>SRC Active</span>";
        } else {
            echo "<span class='inactive'>SRC Inactive</span>";
        }
        echo "</p>";

        // Action buttons
        echo "<div class='action-buttons'>";
        echo "<form action='CandidateApprovedProcess.php' method='post'>";
        echo "<input type='hidden' name='candidate_name' value='" . htmlspecialchars($row["StudentID"]) . "'>";
        echo "<button type='submit' class='Approve-Button'>Approve Candidate</button>";
        echo "</form>";

        echo "<form action='CandidateUnapprovedProcess.php' method='post'>";
        echo "<input type='hidden' name='candidate_name' value='" . htmlspecialchars($row["StudentID"]) . "'>";
        echo "<button type='submit' class='Unapprove-Button'>Unapprove Candidate</button>";
        echo "</form>";

        echo "<form action='SRCApprovedProcess.php' method='post'>";
        echo "<input type='hidden' name='StudentID' value='" . htmlspecialchars($row["StudentID"]) . "'>";
        echo "<button type='submit' class='Approve-Button'>Approve SRC</button>";
        echo "</form>";

        echo "<form action='SRCUnapprovedProcess.php' method='post'>";
        echo "<input type='hidden' name='StudentID' value='" . htmlspecialchars($row["StudentID"]) . "'>";
        echo "<button type='submit' class='Unapprove-Button'>Unapprove SRC</button>";
        echo "</form>";
        echo "</div>"; // Close action buttons

        echo "</div>"; // Close participant info
        echo "</div>"; // Close participant card
    }
    echo "</div>"; // Close participants grid
} else {
    echo "<p>No results found.</p>";
}

$conn->close();
?>
