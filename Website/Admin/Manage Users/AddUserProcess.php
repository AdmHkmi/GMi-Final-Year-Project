<?php
// Include the database connection file
include '../../../Database/DatabaseConnection.php';
// Check if the form is submitted via POST method
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and format input values from the form
    $studentID = strtoupper($_POST['studentID']); // Convert StudentID to uppercase
    $studentEmail = $_POST['studentEmail']; // Get the email
    $studentPassword = password_hash($_POST['studentPassword'], PASSWORD_DEFAULT); // Hash the password
    $studentName = $_POST['studentName']; // Get the student's name
    $profilePicture = "Default.jpg"; // Set a default profile picture
    $userApproval = isset($_POST['userApproval']) ? 1 : 0; // Check if user approval is set
    // Prepare a SQL statement to check for existing StudentID or StudentEmail
    $checkIDSql = "SELECT StudentID FROM VSStudents WHERE StudentID = ? OR StudentEmail = ?";
    $stmtID = $conn->prepare($checkIDSql); // Prepare the statement
    $stmtID->bind_param("ss", $studentID, $studentEmail); // Bind parameters
    $stmtID->execute(); // Execute the statement
    $stmtID->store_result(); // Store the result for checking
    // Check if any results were found
    if ($stmtID->num_rows > 0) {
        // If StudentID or Email exists, show an alert and redirect
        echo '<script>alert("This StudentID or Email is already signed up."); window.location.href = "AddUser.php";</script>';
        $stmtID->close(); // Close the statement
        exit; // Exit to prevent further execution
    }
    // Prepare SQL statement to insert a new user into the VSStudents table
    $sql = "INSERT INTO VSStudents (StudentID, StudentEmail, StudentPassword, StudentName, StudentProfilePicture, UserApproval) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql); // Prepare the statement
    $stmt->bind_param("sssssi", $studentID, $studentEmail, $studentPassword, $studentName, $profilePicture, $userApproval); // Bind parameters
    // Execute the insert statement
    if ($stmt->execute()) {
        // Prepare to insert the new user into the VSVote table
        $sqlVote = "INSERT INTO VSVote (StudentID, StudentEmail, StudentName, StudentProfilePicture) 
                    VALUES (?, ?, ?, ?)";
        $stmtVote = $conn->prepare($sqlVote); // Prepare the vote insertion statement
        $stmtVote->bind_param("ssss", $studentID, $studentEmail, $studentName, $profilePicture); // Bind parameters for vote insertion
        // Execute the vote insertion statement
        if ($stmtVote->execute()) {
            // If successful, show a success alert
            echo '<script>alert("New user added successfully."); window.location.href = "AddUser.php";</script>';
        } else {
            // If there is an error creating the vote record, show an alert with the error message
            echo '<script>alert("User added, but error creating vote record: ' . $stmtVote->error . '"); window.location.href = "AddUser.php";</script>';
        }
        $stmtVote->close(); // Close the vote statement
    } else {
        // If there is an error inserting the user, show an alert with the error message
        echo '<script>alert("Error: ' . $stmt->error . '"); window.location.href = "AddUser.php";</script>';
    }
    $stmtID->close(); // Close the ID check statement
    $stmt->close(); // Close the user insertion statement
}
// Close the database connection
$conn->close();
?>
