<?php
// Include database connection
include '../../../Database/DatabaseConnection.php';

// Include PHPMailer files
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../../../phpmailer/vendor/phpmailer/phpmailer/src/Exception.php';
require '../../../phpmailer/vendor/phpmailer/phpmailer/src/PHPMailer.php';
require '../../../phpmailer/vendor/phpmailer/phpmailer/src/SMTP.php';

// Fetch all students with approved SRC candidacy from VSVote table
$studentQuery = "SELECT StudentEmail, StudentName FROM VSVote WHERE SRCApproval = 1";
$studentResult = $conn->query($studentQuery);

// Check if there are approved SRC candidates
if ($studentResult->num_rows == 0) {
    echo "<script>alert('No approved SRC candidates to notify.'); window.location.href='ManageParticipants.php';</script>";
    exit; // Stop execution if no approved candidates
}

// Initialize PHPMailer
$mail = new PHPMailer(true);

try {
    // SMTP server settings
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com'; // SMTP server
    $mail->SMTPAuth = true;
    $mail->Username = 'adamhakimi6670i@gmail.com'; // SMTP username
    $mail->Password = 'iwtuokefcymlmjzz'; // SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // Sender information
    $mail->setFrom('adamhakimi6670i@gmail.com', 'GMi Voting System');

    // Email body for SRC candidates
    $mail->Subject = "Congratulations on Your SRC Candidacy Approval";
    $mail->isHTML(true);
    $mail->Body = "
    <h3>Congratulations!</h3>
    <p>Dear Student,</p>
    <p>We are thrilled to inform you that your candidacy for the Student Representative Council (SRC) has been successfully approved. After careful consideration, your application stood out, demonstrating the qualities and commitment we seek in our student representatives.</p>
    <p>Becoming a part of the SRC is both an accomplishment and a responsibility. As a member, you will have the opportunity to make meaningful contributions, share student perspectives, and help shape a positive experience for all at GMi. We believe your unique ideas and dedication will bring valuable insight and energy to the council.</p>
    <p>Thank you for your enthusiasm, and we look forward to seeing the positive impact you will make. Once again, congratulations on this well-deserved recognition!</p>
    <p>For futher information, please login to <a href='http://www.gmisrce-election.my/'>GMi SRC E-Election.</a></p>
    <p>Warm regards,<br>The GMi SRC E-election Team</p>
    ";

    // Send email to each approved SRC candidate
    while ($student = $studentResult->fetch_assoc()) {
        $mail->addAddress($student['StudentEmail']);

        if (!$mail->send()) {
            echo "Error sending email to " . $student['StudentEmail'] . ": " . $mail->ErrorInfo . "<br>";
        }

        // Clear recipients after each email
        $mail->clearAddresses();
    }

    // Alert message for successful email sending
    echo "<script>alert('Emails sent successfully to all approved SRC.'); window.location.href='ManageParticipants.php';</script>";
} catch (Exception $e) {
    echo "Error sending email: {$mail->ErrorInfo}";
}

// Close database connection
$conn->close();
?>
