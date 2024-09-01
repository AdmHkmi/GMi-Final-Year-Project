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

// Check if the student already exists in VSCurrentSRC
$checkSql = "SELECT COUNT(*) FROM VSCurrentSRC WHERE StudentID = ?";
$checkStmt = $conn->prepare($checkSql);
$checkStmt->bind_param("s", $studentID);
$checkStmt->execute();
$checkStmt->bind_result($count);
$checkStmt->fetch();
$checkStmt->close();

if ($count > 0) {
    // Student already exists in VSCurrentSRC
    echo "<script>alert('The desired user already approved'); window.location.href = 'ManageParticipants.php';</script>";
    $conn->close();
    exit();
}

// Prepare the SQL statement to fetch student data from VSStudents
$sql = "SELECT StudentID, StudentEmail, StudentName, StudentProfilePicture, Manifesto 
        FROM VSStudents 
        WHERE StudentID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $studentID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Fetch the data
    $row = $result->fetch_assoc();
    
    // Prepare the SQL statement to insert data into VSCurrentSRC
    $insertSql = "INSERT INTO VSCurrentSRC (StudentID, StudentEmail, StudentName, StudentProfilePicture, Manifesto)
                  VALUES (?, ?, ?, ?, ?)";
    $insertStmt = $conn->prepare($insertSql);
    $insertStmt->bind_param("sssss", 
        $row['StudentID'], 
        $row['StudentEmail'], 
        $row['StudentName'], 
        $row['StudentProfilePicture'], 
        $row['Manifesto']
    );

    if ($insertStmt->execute()) {
        // Redirect to ManageParticipants.php on success
        echo "<script>alert('SRC approved successfully!'); window.location.href = 'ManageParticipants.php';</script>";
    } else {
        echo "Error: " . $insertStmt->error;
    }

    $insertStmt->close();
} else {
    echo "Student not found.";
}

$stmt->close();
$conn->close();
?>
