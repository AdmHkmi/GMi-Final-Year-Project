<?php
include '../../../Database/DatabaseConnection.php'; // Include database connection

// Ensure the user is logged in
$loggedInUser = isset($_SESSION['StudentID']) ? $_SESSION['StudentID'] : null;
if ($loggedInUser === null) {
    header("Location: ../LoginPage.php"); // Redirect to login if not logged in
    exit();
}

// Fetch the voting history for the logged-in user
$sql = "SELECT VSVoteHistory.CandidateID, VSStudents.StudentName, VSStudents.StudentProfilePicture 
        FROM VSVoteHistory 
        JOIN VSStudents ON VSVoteHistory.CandidateID = VSStudents.StudentID 
        WHERE VSVoteHistory.VoterID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $loggedInUser);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vote History</title>
    <link rel="stylesheet" href="CandidateVoteHistory.css"> <!-- Link to CSS file -->
</head>
<body>
    <div class="VoteHistory">
    <h2>Thanks for your participation!</h2> 
        <p>Your Vote History</p>
        <?php if ($result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Candidate Profile Picture</th>
                        <th>Candidate Name</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <img src="../../../ProfilePicture/<?php echo htmlspecialchars($row['StudentProfilePicture']); ?>" alt="Candidate Picture" style="width: 50px; height: 50px; border-radius: 50%;">
                            </td>
                            <td><?php echo htmlspecialchars($row['StudentName']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>You have not voted for any candidates yet.</p>
        <?php endif; ?>
    </div>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
