<?php
// Include database connection
include '../../../Database/DatabaseConnection.php';

// Include PHPMailer files
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../../../phpmailer/vendor/phpmailer/phpmailer/src/Exception.php';
require '../../../phpmailer/vendor/phpmailer/phpmailer/src/PHPMailer.php';
require '../../../phpmailer/vendor/phpmailer/phpmailer/src/SMTP.php';

// Check if the EventID is set in the POST request
if (isset($_POST['EventID'])) {
    $eventID = $_POST['EventID'];

    // Fetch the event details from VSEvents table
    $eventQuery = "SELECT EventName, StartDate, EndDate, isActive FROM VSEvents WHERE EventID = ?";
    $stmt = $conn->prepare($eventQuery);
    $stmt->bind_param("i", $eventID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $event = $result->fetch_assoc();
        $eventName = $event['EventName'];
        $startDate = $event['StartDate'];
        $endDate = $event['EndDate'];
        $isActive = $event['isActive'];

        // Fetch all students with a valid email and userapproval = 1 from VSStudents table
        $studentQuery = "SELECT StudentEmail FROM VSStudents WHERE StudentEmail IS NOT NULL AND userapproval = 1";
        $studentResult = $conn->query($studentQuery);

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

            // Determine email content based on event type
            if ($eventName == 'Nomination Vote' || $eventName == 'SRC Vote') {
                // Check if StartDate or EndDate is NULL
                if ($startDate === NULL || $endDate === NULL) {
                    echo "<script>alert('The event start date or end date is not set. Emails cannot be sent.'); window.location.href='ManageEvents.php';</script>";
                    exit; // Stop execution if dates are not set
                }

                // Email body for upcoming events
                $mail->Subject = "Event Notification: $eventName";
                $mail->isHTML(true);
                $mail->Body = "
                    <h3>Important Event Notification</h3>
                    <p>Dear Student,</p>
                    <p>We are excited to inform you about an upcoming event organized by the GMi Voting System. Below are the event details:</p>
                    <p><strong>Event Name:</strong> $eventName</p>
                    <p><strong>Start Date:</strong> " . date('F j, Y, g:i A', strtotime($startDate)) . "</p>
                    <p><strong>End Date:</strong> " . date('F j, Y, g:i A', strtotime($endDate)) . "</p>
                    <p>We encourage you to participate and make your voice heard during this voting period. For more details, please log in to your student account on the GMi Voting System portal.</p>
                    <p>Thank you, and we look forward to your active involvement!</p>
                    <p>Best regards,<br>GMi Voting System Team</p>
                ";
            } elseif ($eventName == 'SRC Result' || $eventName == 'Nomination Result') {
                // Email body for results notifications
                if ($isActive == 1) {
                    $mail->Subject = "Results Notification: $eventName";
                    $mail->isHTML(true);
                    $mail->Body = "
                        <h3>Important Result Notification</h3>
                        <p>Dear Student,</p>
                        <p>We are pleased to inform you that the results for the recent voting events have been published. Below are the details:</p>
                        <p><strong>Event Name:</strong> $eventName</p>
                        <p>The results have been finalized, and you can now log in to your student account on the GMi Voting System portal to view the detailed results and outcomes.</p>
                        <p>Thank you for your participation, and we encourage you to check the results!</p>
                        <p>Best regards,<br>GMi Voting System Team</p>
                    ";
                } else {
                    // Alert if the event is not active
                    echo "<script>alert('The event is not active. Emails cannot be sent.'); window.location.href='ManageEvents.php';</script>";
                    exit; // Stop the execution if not eligible
                }
            } else {
                // Content for other events
                if ($startDate === NULL || $endDate === NULL) {
                    echo "<script>alert('The event start date or end date is not set for this event. Emails cannot be sent.'); window.location.href='ManageEvents.php';</script>";
                    exit; // Stop execution if dates are not set
                }

                // Email body for other events
                $mail->Subject = "Event Notification: $eventName";
                $mail->isHTML(true);
                $mail->Body = "
                    <h3>Upcoming Event Notification</h3>
                    <p>Dear Student,</p>
                    <p>We would like to inform you about an upcoming event organized by the GMi Voting System. Below are the event details:</p>
                    <p><strong>Event Name:</strong> $eventName</p>
                    <p><strong>Start Date:</strong> " . date('F j, Y, g:i A', strtotime($startDate)) . "</p>
                    <p><strong>End Date:</strong> " . date('F j, Y, g:i A', strtotime($endDate)) . "</p>
                    <p>For more details, please log in to your student account on the GMi Voting System portal.</p>
                    <p>Best regards,<br>GMi Voting System Team</p>
                ";
            }

            // Send email to each student
            while ($student = $studentResult->fetch_assoc()) {
                $mail->addAddress($student['StudentEmail']);

                if (!$mail->send()) {
                    echo "Error sending email to " . $student['StudentEmail'] . ": " . $mail->ErrorInfo . "<br>";
                }

                // Clear recipients after each email
                $mail->clearAddresses();
            }

            // Alert message for successful email sending
            echo "<script>alert('Emails sent successfully to all students.'); window.location.href='ManageEvents.php';</script>";
        } catch (Exception $e) {
            echo "Error sending email: {$mail->ErrorInfo}";
        }
    } else {
        echo "Event not found.";
    }

    // Close statement
    $stmt->close();
}

// Close database connection
$conn->close();
?>
