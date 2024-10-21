<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../phpmailer/vendor/autoload.php'; // Path to Composer's autoload.php
include '../../Database/DatabaseConnection.php';
include 'EmailUserVerification.php'; // Include the email function file

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get values from form
    $StudentName = trim($_POST['StudentName']);
    $StudentID = strtoupper(trim($_POST['StudentID'])); // Convert to uppercase
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

    // Hash the password before storing it
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

    // Insert the user into the database with UserApproval set to false
    $sql = "INSERT INTO VSStudents (StudentName, StudentID, StudentEmail, StudentPassword, StudentProfilePicture, UserApproval) 
            VALUES (?, ?, ?, ?, 'Default.jpg', false)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $StudentName, $StudentID, $StudentEmail, $hashedPassword);

    if ($stmt->execute()) {
        // Generate a unique token for verification
        $token = bin2hex(random_bytes(16));

        // Store the token in the database
        $sqlToken = "UPDATE VSStudents SET VerificationToken = ? WHERE StudentEmail = ?";
        $stmtToken = $conn->prepare($sqlToken);
        $stmtToken->bind_param("ss", $token, $StudentEmail);
        $stmtToken->execute();

        // Insert related data into VSVote
        $profilePicture = 'Default.jpg';
        $sqlVote = "INSERT INTO VSVote (StudentID, StudentEmail, StudentName, StudentProfilePicture) 
                    VALUES (?, ?, ?, ?)";
        $stmtVote = $conn->prepare($sqlVote);
        $stmtVote->bind_param("ssss", $StudentID, $StudentEmail, $StudentName, $profilePicture);

        if (!$stmtVote->execute()) {
            echo "Error inserting into VSVote: " . $conn->error;
        }

        // Send verification email
        $emailResult = sendVerificationEmail($StudentName, $StudentEmail, $token);

        if ($emailResult === true) {
            echo '<script>alert("Registration successful! Please check your email for verification."); window.location.href = "../../index.html";</script>';
        } else {
            echo $emailResult;  // Output the error message from the email function
        }

        $stmtToken->close();
        $stmtVote->close();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $stmtID->close();
    $stmt->close();
}

$conn->close();
?>
