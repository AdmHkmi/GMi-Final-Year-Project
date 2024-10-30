<?php
// Include database connection
include '../../../Database/DatabaseConnection.php';

// Include PHPMailer files
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../../../phpmailer/vendor/phpmailer/phpmailer/src/Exception.php';
require '../../../phpmailer/vendor/phpmailer/phpmailer/src/PHPMailer.php';
require '../../../phpmailer/vendor/phpmailer/phpmailer/src/SMTP.php';

// Fetch all approved users from VSStudents
$studentQuery = "SELECT StudentEmail FROM VSStudents WHERE UserApproval = 1";
$studentResult = $conn->query($studentQuery);

// Fetch all news articles from the News table
$newsQuery = "SELECT NewsTitle, NewsContent, NewsImage FROM VSNews"; // Adjust the table name as needed
$newsResult = $conn->query($newsQuery);

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
    $mail->setFrom('adamhakimi6670i@gmail.com', 'GMi News Notification');

    // Check if there are any news articles
    if ($newsResult->num_rows > 0) {
        // Check if there are any approved users
        if ($studentResult->num_rows > 0) {
            // Loop through each news article
            while ($news = $newsResult->fetch_assoc()) {
                $mail->Subject = "New Article: " . $news['NewsTitle'];
                $mail->isHTML(true);
                
                // Email body for the news article
                $mail->Body = "
                    <h3>" . $news['NewsTitle'] . "</h3>
                    <p>" . $news['NewsContent'] . "</p>
                    <p>For more details, <a href='http://localhost/Voting System/'>visit GMi SRC Voting Website.</a></p>
                    <p>Best regards,<br>GMi News Team</p>
                ";

                // Loop through each approved student and send the email
                while ($student = $studentResult->fetch_assoc()) {
                    $mail->addAddress($student['StudentEmail']);
                }

                // Send the email
                if (!$mail->send()) {
                    echo "Error sending email: " . $mail->ErrorInfo . "<br>";
                }

                // Clear recipients after each news article email
                $mail->clearAddresses();
            }

            // Alert message for successful email sending
            echo "<script>alert('Emails sent successfully for all news articles.'); window.location.href='ManageNews.php';</script>";
        } else {
            // Alert if there are no approved users
            echo "<script>alert('There are no approved users to notify.'); window.location.href='ManageNews.php';</script>";
        }
    } else {
        // Alert if there are no news articles
        echo "<script>alert('There are no news articles to notify.'); window.location.href='ManageNews.php';</script>";
    }
} catch (Exception $e) {
    echo "Error sending email: {$mail->ErrorInfo}";
}

// Close database connection
$conn->close();
?>
