<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../phpmailer/vendor/autoload.php'; // Path to Composer's autoload.php

function sendVerificationEmail($StudentName, $StudentEmail, $token) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();                                            // Send using SMTP
        $mail->Host       = 'gmisrce-election.my';                       // Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
        $mail->Username   = 'adamhakimi6670i@gmail.com';            // SMTP username
        $mail->Password   = 'iwtuokefcymlmjzz';                     // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption
        $mail->Port       = 465;                                    // TCP port to connect to
    
        // Recipients
        $mail->setFrom('adamhakimi6670i@gmail.com', 'GMi Voting System');
        $mail->addAddress($StudentEmail, $StudentName);             // Add a recipient
    
        // Content
        $mail->isHTML(true);                                        // Set email format to HTML
        $mail->Subject = 'Verify Your GMi Voting System Account';
        $mail->Body    = "
            <p>Dear <strong>$StudentName</strong>,</p>  
            <p>Thank you for registering with the <strong>GMi Voting System</strong>. To complete your registration and activate your account, we need to verify your email address.</p>
            <p>Please click the link below to verify your account:</p>
            <p style='text-align: center;'>
                <a href='http://www.gmisrce-election.my/Website/Register/UserVerificationProcess.php?email=$StudentEmail&token=$token' 
                   style='display: inline-block; padding: 10px 20px; color: white; background-color: #007bff; text-decoration: none; border-radius: 5px;'>
                   Verify My Account
                </a>
            </p>
            <p>If you did not create this account, please disregard this email.</p>   
            <p>Best regards,<br>
            <strong>The GMi SRC E-election Team</strong></p> 
            <p style='font-size: 12px; color: #666;'>This is an automated message, please do not reply to this email.</p>
        ";  
        $mail->send();
        return true;
    }
     catch (Exception $e) {
        return "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
?>
