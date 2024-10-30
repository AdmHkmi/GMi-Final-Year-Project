<?php
session_start(); // Start the session

include '../../../Database/DatabaseConnection.php';

// Set PHP timezone to match your server's timezone
date_default_timezone_set('Asia/Kuala_Lumpur'); // Adjust as per your timezone

// Retrieve StudentID from URL parameter
$StudentID = $_GET['StudentID'];


$sql = "SELECT StudentID, StudentProfilePicture, StudentName, Manifesto FROM VSVote WHERE StudentID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $StudentID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $ViewStudentID = $row['StudentID'];
    $candidateName = $row['StudentName'];
    $profilePicture = $row['StudentProfilePicture'];
    $manifesto = $row['Manifesto'];

    // Check if manifesto is NULL or empty
    if ($manifesto === null || $manifesto === "") {
        $manifesto = "The manifesto has not been announced yet.";
    }

} else {
    $ViewStudentID = "Student ID Not Found";
    $candidateName = "Candidate Not Found";
    $profilePicture = ""; // Provide default or error image path
    $manifesto = "Manifesto not available.";
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Participants</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="ViewSRCDetails.css">
</head>
<body>
<div class="background-image"></div>
<header class="header">
    <div class="logo-container"><a href="../Home Page/AdminHomepage.php"><img src="../../../Images/GMiLogo.png" class="GMiLogo" alt="GMi Logo"></a></div>
    <nav class="navbar">
        <ul>
            <li><a href="../Manage Events/ManageEvents.php">Manage Events</a></li>
            <li><a href="../Manage Users/ManageUsers.php">Manage Users</a></li>
            <li><a href="ManageParticipants.php">Manage Participants</a></li>
            <li><a href="../Manage Result/ManageResult.php">Manage Results</a></li>
            <li><a href="../Manage News/ManageNews.php">Manage News</a></li>
            <li><a href="../Generate Report/GenerateReport.php">Generate Report</a></li>
        </ul>
    </nav>
    <div class="top-right-buttons"><a href="ManageParticipants.php"><button class="back-button"><i class='fas fa-arrow-left'></i></button></a> </div>
</header>
<div class="CandidateDetails">
<b><i>The details of <?php echo htmlspecialchars($candidateName); ?>.</i></b><br>
</div>
<main>
    <div class="candidate-details-container">
        <table>
            <tr><th>Profile Picture</th><th>Details</th></tr>
            <tr><td rowspan="7"><img src="../../../ProfilePicture/<?php echo htmlspecialchars($profilePicture); ?>" alt="Profile Picture" width='100' height='100'></td>
            <tr><td><b>Student ID</b></td></tr>
            <tr><td><?php echo htmlspecialchars($ViewStudentID); ?></td></tr>
            <tr><td><b>Name</b></td></tr>
            <tr><td><?php echo htmlspecialchars($candidateName); ?></td></tr>
            <tr><td><b>Manifesto</b></td></tr>
            <tr><td><?php echo htmlspecialchars($manifesto); ?></td></tr>  
        </table>
    </div>
</main>
<footer></footer>
</body>
</html>
