<?php
// Include database connection
include '../../../Database/DatabaseConnection.php';

// Include PHPMailer files
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../../../phpmailer/vendor/phpmailer/phpmailer/src/Exception.php';
require '../../../phpmailer/vendor/phpmailer/phpmailer/src/PHPMailer.php';
require '../../../phpmailer/vendor/phpmailer/phpmailer/src/SMTP.php';

// Fetch all students with approved candidacy from VSVote table
$studentQuery = "SELECT StudentEmail, StudentName FROM VSVote WHERE CandidateApproval = 1";
$studentResult = $conn->query($studentQuery);

// Check if there are approved candidates
if ($studentResult->num_rows == 0) {
    echo "<script>alert('No approved candidates to notify.'); window.location.href='ManageParticipants.php';</script>";
    exit; // Stop execution if no approved candidates
}

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

    // Email body for candidates
    $mail->Subject = "Congratulations on Your Candidacy Approval";
    $mail->isHTML(true);
    $mail->Body = "
        <h3>Congratulations!</h3>
        <p>Dear Student,</p>
        <p>We are pleased to inform you that your candidacy has been approved for the upcoming voting events organized by the GMi Voting System.</p>
        <p>We encourage you to prepare and engage with your fellow students to promote your manifesto.</p>
        <p>You can update your SRC profile on the <a href='http://www.gmisrce-election.my/'>GMi SRC E-Election.</a></p>
        <p>Thank you for your participation!</p>
        <p>Best regards,<br>GMi SRC E-election Team</p>
    ";

    // Send email to each approved candidate
    while ($student = $studentResult->fetch_assoc()) {
        $mail->addAddress($student['StudentEmail']);

        if (!$mail->send()) {
            echo "Error sending email to " . $student['StudentEmail'] . ": " . $mail->ErrorInfo . "<br>";
        }

        // Clear recipients after each email
        $mail->clearAddresses();
    }

    // Alert message for successful email sending
    echo "<script>alert('Emails sent successfully to all approved candidates.'); window.location.href='ManageParticipants.php';</script>";
} catch (Exception $e) {
    echo "Error sending email: {$mail->ErrorInfo}";
}

// Close database connection
$conn->close();
?>
