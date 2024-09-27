<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../phpmailer/vendor/autoload.php'; // Path to Composer's autoload.php

include '../../Database/DatabaseConnection.php';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get values from form
    $StudentName = $_POST['StudentName'];
    $StudentID = $_POST['StudentID'];
    $StudentEmail = $_POST['StudentEmail'];
    $StudentPassword = $_POST['StudentPassword'];

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
    $stmt->bind_param("ssss", $StudentName, $StudentID, $StudentEmail, $StudentPassword);  // Bind four parameters

    if ($stmt->execute()) {
        // Generate a unique token for verification
        $token = bin2hex(random_bytes(16));

        // Store the token in the database
        $sqlToken = "UPDATE VSStudents SET VerificationToken = ? WHERE StudentEmail = ?";
        $stmtToken = $conn->prepare($sqlToken);
        $stmtToken->bind_param("ss", $token, $StudentEmail);
        $stmtToken->execute();

        // Send verification email
        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail->isSMTP();                                            // Send using SMTP
            $mail->Host       = 'smtp.gmail.com';                     // Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
            $mail->Username   = 'adamhakimi6670i@gmail.com';               // SMTP username
            $mail->Password   = 'iwtuokefcymlmjzz';                  // SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` also accepted
            $mail->Port       = 587;                                    // TCP port to connect to

            //Recipients
            $mail->setFrom('adamhakimi6670i@gmail.com', 'GMi Voting System');
            $mail->addAddress($StudentEmail, $StudentName);             // Add a recipient

            // Content
            $mail->isHTML(true);                                        // Set email format to HTML
            $mail->Subject = 'Account Verification';
            $mail->Body    = "Dear $StudentName,<br><br>Thank you for registering. Please click the link below to verify your account:<br><br>
            <a href='http://localhost/Voting System/Website/Register/UserVerification.php?email=$StudentEmail&token=$token'>Click me to verify</a><br><br>
            Best regards,<br>GMi Voting System";
            $mail->send();
            echo '<script>alert("Registration successful! Please check your email for verification."); window.location.href = "../../index.html";</script>';
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }

        $stmtToken->close();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $stmtID->close();
    $stmt->close();
}

$conn->close();
?>
