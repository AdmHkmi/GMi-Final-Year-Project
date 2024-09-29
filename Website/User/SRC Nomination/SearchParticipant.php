<?php
include '../../../Database/DatabaseConnection.php';

// Initialize search variable
$search = isset($_POST['search']) ? $_POST['search'] : '';

// Prepare the SQL statement to prevent SQL injection
$sql = "SELECT StudentID, StudentProfilePicture, StudentName FROM VSStudents 
        WHERE (StudentName LIKE ? OR StudentID LIKE ?) AND UserApproval = 1";

$stmt = $conn->prepare($sql);
$search_param = "%" . $search . "%";
$stmt->bind_param("ss", $search_param, $search_param);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "<br>";
    echo "<link rel='stylesheet' href='SRCNominationPage.css'>";
    echo "<br><center>Search results for '" . htmlspecialchars($search) . "'</center>";
    echo "<div class='SRC-Container'>"; // Ensure the container class matches with SRCNominationPage.php
    while ($row = $result->fetch_assoc()) {
        echo "<div class='candidate-card'>"; // Ensure the card class matches with SRCNominationPage.php
        echo "<img src='../../../ProfilePicture/". htmlspecialchars($row["StudentProfilePicture"])."' width='100' height='100' alt='Profile Picture'>";
        echo "<h3>".htmlspecialchars($row["StudentName"])."</h3>";
        echo "<p>Student ID: ".htmlspecialchars($row["StudentID"])."</p>";
        echo '<form id="form_'.$row["StudentID"].'" method="post" action="SRCNominationPage.php">'; // Change action to SRCNominationPage.php
        echo '<input type="hidden" name="StudentID" value="'.$row["StudentID"].'">'; // Ensure the name attribute matches with SRCNominationPage.php
        echo '<button id="vote_button_'.$row["StudentID"].'" type="button" onclick="confirmVote(\''.$row["StudentID"].'\')" class="vote-button">Vote</button>'; // Ensure the ID and class match with SRCNominationPage.php
        echo '</form>';
        echo "</div>";
    }
    echo "</div>";
} else {
    echo "<br><center>No user matching your search.</center>";
}

$stmt->close();
$conn->close();
?>
