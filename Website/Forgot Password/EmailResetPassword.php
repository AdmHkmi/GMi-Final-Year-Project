<?php

// Use the PHPMailer classes for sending email
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
// Load Composer's autoload file to include PHPMailer
require '../../phpmailer/vendor/autoload.php'; // Path to Composer's autoload.php

// Retrieve the email address submitted from the form
$email = $_POST["email"];

// Generate a secure random token
$token = bin2hex(random_bytes(16));

// Hash the token using SHA-256 to store it securely
$token_hash = hash("sha256", $token);

// Set the token expiry time (30 minutes from now)
$expiry = date("Y-m-d H:i:s", time() + 60 * 30);

// Include the database connection file
include '../../Database/DatabaseConnection.php';

// Prepare an SQL statement to update the reset password token and its expiry
$sql = "UPDATE VSStudents
        SET ResetPasswordToken = ?,
            ResetPasswordTokenExpired = ?
        WHERE StudentEmail = ?";

$stmt = $conn->prepare($sql); // Use the established database connection

// Bind the hashed token, expiry date, and email to the prepared statement
$stmt->bind_param("sss", $token_hash, $expiry, $email);

// Execute the statement to update the database
$stmt->execute();

// Check if any rows were affected (meaning the update was successful)
if ($conn->affected_rows) {  // Use the established database connection

    // Create a new instance of PHPMailer
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();                                            // Send using SMTP
        $mail->Host       = 'smtp.gmail.com';                     // Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
        $mail->Username   = 'adamhakimi6670i@gmail.com';            // SMTP username (should be secured in production)
        $mail->Password   = 'iwtuokefcymlmjzz';                     // SMTP password (should be secured in production)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption
        $mail->Port       = 587;                                    // TCP port to connect to

        // Recipients
        $mail->setFrom("noreply@example.com", "GMi Voting System"); // Sender's email and name
        $mail->addAddress($email);                                  // Add the recipient's email

        // Content
        $mail->isHTML(true);                                        // Set email format to HTML
        $mail->Subject = "Password Reset Request";                  // Email subject
        $mail->Body    = <<<END
            <html>
            <head>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        background-color: #f4f4f4;
                        margin: 0;
                        padding: 20px;
                        color: #000; /* Set default font color to black */
                    }
                    .container {
                        background-color: #fff;
                        border-radius: 5px;
                        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                        padding: 20px;
                        max-width: 600px;
                        margin: auto;
                    }
                    .header {
                        text-align: center;
                        padding: 10px 0;
                    }
                    .content {
                        line-height: 1.6;
                    }
                    .footer {
                        text-align: center;
                        font-size: 0.9em;
                        color: #777;
                    }
                    a {
                        background-color: #007BFF; /* Set button background color */
                        color: #000; /* Set button text color to black */
                        padding: 10px 15px;
                        text-decoration: none;
                        border-radius: 5px;
                        display: inline-block;
                        margin-top: 20px;
                    }
                </style>
            </head>
            <body>
                <div class="container">
                    <div class="header">
                        <h2>Password Reset Request</h2>
                    </div>
                    <div class="content">
                        <p>Dear User,</p>
                        <p>We received a request to reset your password for your account associated with <strong>$email</strong>.</p>
                        <p>If you did not make this request, please ignore this email.</p>
                        <p>If you would like to reset your password, please click the button below:</p>
                        <p><a href="http://www.gmisrce-election.my/Website/Forgot Password/ResetPassword.php?token=$token">Reset Password</a></p>
                        <p>This link will expire in 30 minutes.</p>
                        <p>Thank you,</p>
                        <p>The GMi SRC E-election Team</p>
                    </div>
                    <div class="footer">
                        <p>&copy; 2024 GMi SRC E-election. All rights reserved.</p>
                    </div>
                </div>
            </body>
            </html>
END;

        // Send the email
        $mail->send();

        // Use JavaScript to display a message to the user and redirect
        echo "<script>
                alert('Message sent, please check your Email.');
                window.location.href = '../../index.html'; // Redirect to the index page
              </script>";
    } catch (Exception $e) {
        // Handle errors while sending the email
        echo "Message could not be sent. Mailer error: {$mail->ErrorInfo}";
    }
} else {
    // If no rows were affected, alert the user that the email does not exist
    echo "<script>
            alert('Email address does not exist in our records.');
            window.location.href = '../../index.html'; // Redirect to the index page
          </script>";
}
?>
