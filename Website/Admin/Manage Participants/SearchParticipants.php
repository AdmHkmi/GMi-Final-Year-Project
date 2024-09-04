<?php

include '../../../Database/DatabaseConnection.php';

// Get search term from POST request and sanitize input
$searchTerm = isset($_POST['search']) ? $conn->real_escape_string($_POST['search']) : '';

// Query to get all candidates and SRC data based on search term
$sql = "SELECT StudentProfilePicture, StudentName, StudentEmail, StudentID, 
                TotalCandidateVote, 
                (SELECT COUNT(*) FROM VSCurrentCandidate WHERE StudentID = VSStudents.StudentID) AS CandidateStatus,
                TotalSRCVote,
                (SELECT COUNT(*) FROM VSCurrentSRC WHERE StudentID = VSStudents.StudentID) AS SRCStatus
        FROM VSStudents 
        WHERE UserApproval = 1 
        AND (StudentName LIKE '%$searchTerm%' OR StudentID LIKE '%$searchTerm%')
        ORDER BY TotalCandidateVote DESC";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Output data of each row
    echo "<table border='1' align='center'>";
    echo "<tr>";
    echo "<th>Profile Picture</th>";
    echo "<th>Student Name</th>";
    echo "<th>Student Email</th>";
    echo "<th>StudentID</th>";
    echo "<th>Total Candidate Vote</th>";
    echo "<th>Candidate Status</th>";
    echo "<th>Total SRC Vote</th>";
    echo "<th>SRC Status</th>";
    echo "<th>Action</th>";
    echo "</tr>";

    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td align='center'><img src='../../../ProfilePicture/" . $row["StudentProfilePicture"] . "' alt='Profile Picture' style='width: 150px; height: 150px;'></td>";
        echo "<td>" . htmlspecialchars($row["StudentName"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["StudentEmail"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["StudentID"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["TotalCandidateVote"]) . "</td>";

        // Display Candidate Status
        echo "<td>";
        if ($row["CandidateStatus"] > 0) {
            echo "<span style='color: green;'>Active</span>";
        } else {
            echo "<span style='color: red;'>Inactive</span>";
        }
        echo "</td>";

        echo "<td>" . htmlspecialchars($row["TotalSRCVote"]) . "</td>";

        // Display SRC Status
        echo "<td>";
        if ($row["SRCStatus"] > 0) {
            echo "<span style='color: green;'>Active</span>";
        } else {
            echo "<span style='color: red;'>Inactive</span>";
        }
        echo "</td>";

        echo "<td>";
        echo "<form action='CandidateApprovedProcess.php' method='post' style='display:inline;'>";
        echo "<input type='hidden' name='candidate_name' value='" . htmlspecialchars($row["StudentID"]) . "'>";
        echo "<input type='submit' value='Approve Candidate' onclick=\"return confirm('Are you sure you want to approve the desired user?')\">";
        echo "</form>";
        echo "<form action='CandidateUnapprovedProcess.php' method='post' style='display:inline;'>";
        echo "<input type='hidden' name='candidate_name' value='" . htmlspecialchars($row["StudentID"]) . "'>";
        echo "<input type='submit' value='Unapprove Candidate' onclick=\"return confirm('Are you sure you want to unapprove the desired user?')\">";
        echo "</form>";
        echo "<form action='SRCApprovedProcess.php' method='post' style='display:inline;'>";
        echo "<input type='hidden' name='StudentID' value='" . htmlspecialchars($row["StudentID"]) . "'>";
        echo "<button type='submit' onclick=\"return confirm('Are you sure you want to approve the desired user?')\">Approve SRC</button>";
        echo "</form>";
        echo "<form action='SRCUnapprovedProcess.php' method='post' style='display:inline;'>";
        echo "<input type='hidden' name='StudentID' value='" . htmlspecialchars($row["StudentID"]) . "'>";
        echo "<button type='submit' onclick=\"return confirm('Are you sure you want to unapprove the desired user?')\">Unapprove SRC</button>";
        echo "</form>";
        echo "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No results found for '$searchTerm'.";
}

$conn->close();
?>
