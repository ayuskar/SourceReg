<?php
// Import PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

function sendVerificationEmail($email, $username, $verification_token, $user_id) {
    $mail = new PHPMailer();
    $mail->SMTPDebug = 0; 

    try {
        // Server settings
        $mail->SMTPDebug = 0; // Enable verbose debug output
        $mail->isSMTP(); // Send using SMTP
        $mail->Host = 'smtp.gmail.com'; // Set the SMTP server to send through
        $mail->SMTPAuth = true; // Enable SMTP authentication
        $mail->Username = ''; // Enter your email
        $mail->Password = ''; // SMTP password (Generated App Password)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Enable implicit TLS encryption
        $mail->Port = 465; // TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

        // Recipients
        $mail->setFrom('your email', 'SourceReg');
        $mail->addAddress($email, $username); // Use the user's email and username

        // Content
        $mail->isHTML(true); // Set email format to HTML
        $mail->Subject = 'Verify your email address on SourceReg';
        $mail->Body = "<h2>Hello $username!</h2>
        <p>To complete your registration on SourceReg, please verify your email address by clicking the link below:</p>
        <p><a href='http://localhost/SourceReg/verify-email.php?token=$verification_token&user_id=$user_id'>Verify Email</a></p>
        <p>If you did not create an account, please disregard this email.</p>";
        $mail->AltBody = 'Please verify your email address by visiting http://localhost/SourceReg/verify-email.php?token='. $verification_token. '&user_id='. $user_id;


        $mail->send();
        echo 'Verification email sent to ' . $email;
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
