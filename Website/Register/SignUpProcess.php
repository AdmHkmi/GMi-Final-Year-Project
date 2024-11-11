<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../phpmailer/vendor/autoload.php';
include '../../Database/DatabaseConnection.php';
include 'EmailUserVerification.php';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $StudentName = trim($_POST['StudentName']);
    $StudentID = strtoupper(trim($_POST['StudentID']));
    $StudentEmail = trim($_POST['StudentEmail']);
    $StudentPassword = trim($_POST['StudentPassword']);

    // Check if any required fields are empty
    if (empty($StudentName) || empty($StudentID) || empty($StudentEmail) || empty($StudentPassword)) {
        echo '<script>alert("Please fill in all the required fields!"); window.location.href = "RegisterPage.html";</script>';
        exit;
    }

    // Check if the email is from the allowed domain
    $allowedDomain = "@student.gmi.edu.my";
    if (substr($StudentEmail, -strlen($allowedDomain)) !== $allowedDomain) {
        echo '<script>alert("Please use a GMI student email (@student.gmi.edu.my) to register."); window.location.href = "RegisterPage.html";</script>';
        exit;
    }

    // Hash the password
    $hashedPassword = password_hash($StudentPassword, PASSWORD_DEFAULT);

    // Check if the StudentID or StudentEmail already exists
    $checkIDSql = "SELECT StudentID FROM VSStudents WHERE StudentID = ? OR StudentEmail = ?";
    $stmtID = $conn->prepare($checkIDSql);
    $stmtID->bind_param("ss", $StudentID, $StudentEmail);
    $stmtID->execute();
    $stmtID->store_result();

    if ($stmtID->num_rows > 0) {
        echo '<script>alert("This StudentID or Email is already signed up"); window.location.href = "RegisterPage.html";</script>';
        $stmtID->close();
        exit;
    }

    // Generate a unique token for verification
    $token = bin2hex(random_bytes(16));

    // Send verification email
    $emailResult = sendVerificationEmail($StudentName, $StudentEmail, $token);

    if ($emailResult === true) {
        // Insert the user into the database only if email was successfully sent
        $sql = "INSERT INTO VSStudents (StudentName, StudentID, StudentEmail, StudentPassword, StudentProfilePicture, UserApproval, VerificationToken) 
                VALUES (?, ?, ?, ?, 'Default.jpg', false, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $StudentName, $StudentID, $StudentEmail, $hashedPassword, $token);

        if ($stmt->execute()) {
            // Insert related data into VSVote
            $profilePicture = 'Default.jpg';
            $sqlVote = "INSERT INTO VSVote (StudentID, StudentEmail, StudentName, StudentProfilePicture) 
                        VALUES (?, ?, ?, ?)";
            $stmtVote = $conn->prepare($sqlVote);
            $stmtVote->bind_param("ssss", $StudentID, $StudentEmail, $StudentName, $profilePicture);

            if ($stmtVote->execute()) {
                echo '<script>alert("Registration successful! Please check your email for verification."); window.location.href = "../../index.html";</script>';
            } else {
                echo "Error inserting into VSVote: " . $conn->error;
            }

            $stmtVote->close();
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }

        $stmt->close();
    } else {
        echo $emailResult;  // Output the error message from the email function
    }

    $stmtID->close();
}

$conn->close();
?>