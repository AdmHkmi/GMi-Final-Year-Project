<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "VotingSystem";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the StudentID from POST request
$studentID = $_POST['StudentID'];

// Prepare the SQL statement to delete data from VSCurrentSRC
$sql = "DELETE FROM VSCurrentSRC WHERE StudentID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $studentID);

if ($stmt->execute()) {
    // Redirect to ManageParticipants.php on success
    echo "<script>alert('SRC unapproved successfully!'); window.location.href = 'ManageParticipants.php';</script>";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
