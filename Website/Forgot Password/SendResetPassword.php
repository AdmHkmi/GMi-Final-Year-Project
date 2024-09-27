<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../../phpmailer/vendor/autoload.php'; // Path to Composer's autoload.php

$email = $_POST["email"];

$token = bin2hex(random_bytes(16));

$token_hash = hash("sha256", $token);

$expiry = date("Y-m-d H:i:s", time() + 60 * 30);

include '../../Database/DatabaseConnection.php';

$sql = "UPDATE VSStudents
        SET reset_token_hash = ?,
            reset_token_expires_at = ?
        WHERE StudentEmail = ?";

$stmt = $conn->prepare($sql); // Changed $mysqli to $conn

$stmt->bind_param("sss", $token_hash, $expiry, $email);

$stmt->execute();

if ($conn->affected_rows) {  // Changed $mysqli to $conn

    // Create a new PHPMailer instance
    $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail->isSMTP();                                            // Send using SMTP
        $mail->Host       = 'smtp.gmail.com';                     // Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
        $mail->Username   = 'adamhakimi6670i@gmail.com';            // SMTP username
        $mail->Password   = 'iwtuokefcymlmjzz';                     // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption
        $mail->Port       = 587;                                    // TCP port to connect to

        // Recipients
        $mail->setFrom("noreply@example.com", "GMi Voting System");
        $mail->addAddress($email);                                  // Add a recipient

        // Content
        $mail->isHTML(true);                                        // Set email format to HTML
        $mail->Subject = "Password Reset";
        $mail->Body    = <<<END
            Click <a href="localhost/Voting System/Website/Forgot Password/ResetPassword.php?token=$token">here</a> 
            to reset your password.
        END;

        $mail->send();
        echo "Message sent, please check your inbox.";
        header("Refresh:5; url=../../index.html");
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer error: {$mail->ErrorInfo}";
    }
}

?>
