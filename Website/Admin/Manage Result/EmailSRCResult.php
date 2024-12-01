<?php
// Include database connection
include '../../../Database/DatabaseConnection.php';

// Include PHPMailer files
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../../../phpmailer/vendor/phpmailer/phpmailer/src/Exception.php';
require '../../../phpmailer/vendor/phpmailer/phpmailer/src/PHPMailer.php';
require '../../../phpmailer/vendor/phpmailer/phpmailer/src/SMTP.php';

// Fetch all students with approved SRC candidacy from VSStudents table
$studentQuery = "SELECT StudentEmail, StudentName FROM VSStudents WHERE UserApproval = 1";
$studentResult = $conn->query($studentQuery);

// Initialize PHPMailer
$mail = new PHPMailer(true);

try {
    // SMTP server settings
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com'; // SMTP server
    $mail->SMTPAuth = true;
    $mail->Username = 'gmisrceelection@gmail.com'; // SMTP username
    $mail->Password = 'ycwncyptsdelfpzp'; // SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // Sender information
    $mail->setFrom('gmisrceelection@gmail.com', 'GMi Voting System');

    // Fetch the event name for the email body
    $eventName = "SRC Result"; // You can modify this based on your requirements

    // Email body for candidates
    $mail->Subject = "Important SRC Result Notification";
    $mail->isHTML(true);
    $mail->Body = "
        <h3>Important SRC Result Notification</h3>
        <p>Dear Student,</p>
        <p>We are pleased to inform you that the results for the recent SRC voting events have been published. Below are the details:</p>
        <p><strong>Event Name:</strong> $eventName</p>
        <p>The results have been finalized, and you can now log in to your student account on the <a href='http://www.gmisrce-election.my/'>GMi SRC E-Election.</a> to view the detailed results and outcomes.</p>
        <p>Thank you for your participation, and we encourage you to check the results!</p>
        <p>Best regards,<br>GMi SRC E-election Team</p>
    ";

    // Check if there are any approved users
    if ($studentResult->num_rows > 0) {
        // Send email to each approved student
        while ($student = $studentResult->fetch_assoc()) {
            $mail->addAddress($student['StudentEmail']);

            if (!$mail->send()) {
                echo "Error sending email to " . $student['StudentEmail'] . ": " . $mail->ErrorInfo . "<br>";
            }

            // Clear recipients after each email
            $mail->clearAddresses();
        }

        // Alert message for successful email sending
        echo "<script>alert('Emails sent successfully to all students.'); window.location.href='ManageResult.php';</script>";
    } else {
        // Alert if there are no approved users
        echo "<script>alert('There are no approved users to notify.'); window.location.href='ManageResult.php';</script>";
    }
} catch (Exception $e) {
    echo "Error sending email: {$mail->ErrorInfo}";
}

// Close database connection
$conn->close();
?>
