<?php
// Start the session if it hasn't already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Ensure the database connection is included
include '../../../Database/DatabaseConnection.php';

// Check if user is logged in
if (!isset($_SESSION['StudentID'])) {
    echo '<script>alert("You need to be logged in to change your Student ID."); window.location.href = "../../../index.html";</script>';
    exit;
}

// Get the current Student ID from the session
$currentStudentID = $_SESSION['StudentID'];

// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $newStudentID = strtoupper(trim($_POST['newStudentID'])); // Ensure new ID is in uppercase
    $password = trim($_POST['password']);

    if (empty($newStudentID) || empty($password)) {
        echo '<script>alert("Please fill in all fields."); window.location.href = "ChangeStudentID.php";</script>';
        exit;
    }

    // Verify the user's current credentials
    $sql = "SELECT StudentPassword FROM VSStudents WHERE StudentID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $currentStudentID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['StudentPassword'])) {
            // Check if the new Student ID is already taken
            $sqlCheck = "SELECT StudentID FROM VSStudents WHERE StudentID = ?";
            $stmtCheck = $conn->prepare($sqlCheck);
            $stmtCheck->bind_param("s", $newStudentID);
            $stmtCheck->execute();
            $resultCheck = $stmtCheck->get_result();

            if ($resultCheck->num_rows > 0) {
                echo '<script>alert("The new Student ID is already in use. Please choose a different one."); window.location.href = "ChangeStudentID.php";</script>';
            } else {
                // Update the Student ID in the VSStudents table
                $sqlUpdate = "UPDATE VSStudents SET StudentID = ? WHERE StudentID = ?";
                $stmtUpdate = $conn->prepare($sqlUpdate);
                $stmtUpdate->bind_param("ss", $newStudentID, $currentStudentID);

                if ($stmtUpdate->execute()) {
                    // Update the VoterID and CandidateID in the VSVoteHistory table
                    $sqlUpdateVoteHistory = "UPDATE VSVoteHistory 
                                             SET VoterID = CASE WHEN VoterID = ? THEN ? ELSE VoterID END, 
                                                 CandidateID = CASE WHEN CandidateID = ? THEN ? ELSE CandidateID END 
                                             WHERE VoterID = ? OR CandidateID = ?";
                    $stmtVoteHistory = $conn->prepare($sqlUpdateVoteHistory);
                    $stmtVoteHistory->bind_param("ssssss", $currentStudentID, $newStudentID, $currentStudentID, $newStudentID, $currentStudentID, $currentStudentID);

                    if ($stmtVoteHistory->execute()) {
                        // Update session and log out
                        session_destroy();
                        echo '<script>alert("Student ID updated successfully. Please log in again with your new ID."); window.location.href = "../../../index.html";</script>';
                    } else {
                        echo "Error updating vote history: " . $conn->error;
                    }
                } else {
                    echo "Error updating Student ID: " . $conn->error;
                }
            }
        } else {
            echo '<script>alert("Incorrect password. Please try again."); window.location.href = "ChangeStudentID.php";</script>';
        }
    } else {
        echo "User not found.";
    }
}
?>

<!DOCTYPE html>
<!DOCTYPE html>
<html>
<head>
    <title>Change Student ID</title>
    <link rel="icon" type="image/icon" href="../../../Images/favicon.ico">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="ChangeStudentID.css">
</head>
<body>
<div class="background-image"></div>
<header class="header">
    <div class="logo-container">
        <a href="../Home Page/UserHomepage.php">
            <img src="../../../Images/GMiLogo.png" class="GMiLogo" alt="GMi Logo">
        </a>
    </div>
    <div class="top-right-buttons">
        <a href="UserProfilePage.php">
            <button class="back-button"><i class='fas fa-arrow-left'></i></button>
        </a>
    </div>
</header>
    <div class="form-container">
        <h2>Change Student ID</h2>
        <form action="ChangeStudentID.php" method="post" onsubmit="return confirm('Are you sure you want to change your Student ID?')">
            <label for="newStudentID">New Student ID:</label>
            <input type="text" id="newStudentID" name="newStudentID" placeholder="Insert new Student ID" required>
            
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" placeholder="Insert your current password" required>
            <div class="toggle-password-container">
                <input type="checkbox" id="togglePassword" onclick="togglePasswordVisibility('password')">
                <label for="togglePassword">Show Password</label>
            </div>
            <button type="submit" class="submit-button">Update Student ID</button>
        </form>
    </div>
</body>
<script>
    function togglePasswordVisibility(fieldId) {
        const passwordField = document.getElementById(fieldId);
        passwordField.type = passwordField.type === "password" ? "text" : "password";
    }
</script>
</html>

