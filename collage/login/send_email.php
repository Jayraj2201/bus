<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Ensure PHPMailer is installed via Composer

function sendVerificationEmail($email, $code) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; 
        $mail->SMTPAuth = true;
        $mail->Username = 'jayu22012004@gmail.com'; // Use your Gmail
        $mail->Password = 'jtnb fsdf oqnj zkbl'; // Use App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('jayu22012004@gmail.com', 'Your Website'); // Sender email
        $mail->addAddress($email); // Send to registered user email

        $mail->isHTML(true);
        $mail->Subject = 'Email Verification Code';
        $mail->Body = "Your verification code is: <b>$code</b>";

        if ($mail->send()) {
            error_log("Email sent successfully to $email");
            return true;
        } else {
            error_log("Email failed to send to $email: " . $mail->ErrorInfo);
            return false;
        }
    } catch (Exception $e) {
        error_log("Exception in email sending: " . $mail->ErrorInfo);
        return false;
    }
}
?>
