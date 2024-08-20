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

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get values from form (AddUserForm.html)
    $StudentName = $_POST['StudentName'];
    $StudentID = $_POST['StudentID'];
    $StudentEmail = $_POST['StudentEmail'];
    $StudentPassword = $_POST['StudentPassword'];
    $TandC = isset($_POST['TandC']) ? 1 : 0;

    // Check if any required fields are empty
    if (empty($StudentName) || empty($StudentID) || empty($StudentEmail) || empty($StudentPassword)) {
        echo '<script>alert("Please fill in all the required fields!"); window.location.href = "RegisterPage.html";</script>';
    } else {
        // Check if the StudentID already exists
        $checkIDSql = "SELECT StudentID FROM VSStudents WHERE StudentID = ?";
        $stmtID = $conn->prepare($checkIDSql);
        $stmtID->bind_param("s", $StudentID);
        $stmtID->execute();
        $stmtID->store_result();

        if ($stmtID->num_rows > 0) {
            echo '<script>alert("This StudentID is already signed up"); window.location.href = "RegisterPage.html";</script>';
            $stmtID->close();
            exit;
        }

        // Check if the StudentEmail already exists
        $checkEmailSql = "SELECT StudentEmail FROM VSStudents WHERE StudentEmail = ?";
        $stmtEmail = $conn->prepare($checkEmailSql);
        $stmtEmail->bind_param("s", $StudentEmail);
        $stmtEmail->execute();
        $stmtEmail->store_result();

        if ($stmtEmail->num_rows > 0) {
            echo '<script>alert("This StudentEmail is already signed up"); window.location.href = "RegisterPage.html";</script>';
            $stmtEmail->close();
            exit;
        }

        // SQL command to insert into database
        $sql = "INSERT INTO VSStudents (StudentName, StudentID, StudentEmail, StudentPassword, TandC, StudentProfilePicture) VALUES (?, ?, ?, ?, ?, 'Default.jpg')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $StudentName, $StudentID, $StudentEmail, $StudentPassword, $TandC);

        // Show result
        if ($stmt->execute()) {
            echo '<script>alert("Register completed!"); window.location.href = "../../index.html";</script>';
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }

        $stmtID->close();
        $stmtEmail->close();
    }
}

$conn->close();
?>
