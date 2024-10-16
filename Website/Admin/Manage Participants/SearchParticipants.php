<?php
include '../../../Database/DatabaseConnection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $searchTerm = htmlspecialchars($_POST["search"]);

    // Prepare the SQL query to search by StudentName or StudentID
    $sql = "SELECT VSStudents.StudentProfilePicture, 
                   VSStudents.StudentName, 
                   VSStudents.StudentEmail, 
                   VSStudents.StudentID, 
                   VSVote.TotalCandidateVote, 
                   VSVote.TotalSRCVote, 
                   VSVote.CandidateApproval AS CandidateStatus, 
                   VSVote.SRCApproval AS SRCStatus
            FROM VSStudents
            JOIN VSVote ON VSStudents.StudentID = VSVote.StudentID
            WHERE (VSStudents.StudentName LIKE ? OR VSStudents.StudentID LIKE ?)
            AND VSStudents.UserApproval = 1
            ORDER BY VSVote.TotalCandidateVote DESC, VSVote.TotalSRCVote DESC";

    // Prepare the statement
    $stmt = $conn->prepare($sql);

    // Prepare the search term for the LIKE operator
    $searchTermLike = "%" . $searchTerm . "%";

    // Bind the parameters to the SQL query
    $stmt->bind_param("ss", $searchTermLike, $searchTermLike);

    // Execute the statement
    $stmt->execute();

    // Get the result
    $result = $stmt->get_result();

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
            echo "<input type='hidden' name='StudentID' value='" . htmlspecialchars($row["StudentID"]) . "'>";
            echo "<button type='submit' class='Approve-Button'>Approve Candidate</button>";
            echo "</form>";

            echo "<form action='CandidateUnapprovedProcess.php' method='post'>";
            echo "<input type='hidden' name='StudentID' value='" . htmlspecialchars($row["StudentID"]) . "'>";
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
        echo "<p>No results found for \"$searchTerm\".</p>";
    }

    // Close the statement
    $stmt->close();
}

// Close the database connection
$conn->close();
?>
