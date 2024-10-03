<?php
include '../../../Database/DatabaseConnection.php';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $studentID = strtoupper($_POST['studentID']); // Convert to uppercase
    $studentEmail = $_POST['studentEmail'];
    $studentPassword = $_POST['studentPassword']; // Plain text password (not recommended in real apps)
    $studentName = $_POST['studentName'];
    $profilePicture = "Default.jpg"; // Set default profile picture
    $userApproval = isset($_POST['userApproval']) ? 1 : 0; // Assign 1 for approved, 0 for not approved

    // Check if the StudentID or StudentEmail already exists
    $checkIDSql = "SELECT StudentID FROM VSStudents WHERE StudentID = ? OR StudentEmail = ?";
    $stmtID = $conn->prepare($checkIDSql);
    $stmtID->bind_param("ss", $studentID, $studentEmail);
    $stmtID->execute();
    $stmtID->store_result();

    if ($stmtID->num_rows > 0) {
        echo '<script>alert("This StudentID or Email is already signed up."); window.location.href = "AddUser.php";</script>';
        $stmtID->close(); // Close the statement
        exit; // Exit to prevent further execution
    }

    // Insert the new user into the VSStudents table
    $sql = "INSERT INTO VSStudents (StudentID, StudentEmail, StudentPassword, StudentName, StudentProfilePicture, UserApproval) 
            VALUES (?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $studentID, $studentEmail, $studentPassword, $studentName, $profilePicture, $userApproval);

    if ($stmt->execute()) {
        // Prepare to insert into VSVote
        $sqlVote = "INSERT INTO VSVote (StudentID, StudentEmail, StudentName, StudentProfilePicture) 
                    VALUES (?, ?, ?, ?)";
        
        $stmtVote = $conn->prepare($sqlVote);
        $stmtVote->bind_param("ssss", $studentID, $studentEmail, $studentName, $profilePicture);

        if ($stmtVote->execute()) {
            echo "<div class='Message-Container'>New user added successfully, and a vote record has been created.</div>";
        } else {
            echo "<div class='Message-Container'>User added, but error creating vote record: " . $stmtVote->error . "</div>";
        }

        $stmtVote->close(); // Close the vote statement
    } else {
        echo "<div class='Message-Container'>Error: " . $stmt->error . "</div>";
    }

    $stmtID->close(); // Close the ID check statement
    $stmt->close(); // Close the insert statement
}

$conn->close();
?>
