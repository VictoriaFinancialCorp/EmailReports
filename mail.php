<?php

require_once './vendor/autoload.php';
include_once "config.php";


function sendMail($from, $to, $subject, $message){
  $mail = new PHPMailer;

//  $mail->SMTPDebug = 2;                               // Enable verbose debug output

  $mail->isSMTP();                                      // Set mailer to use SMTP
  $mail->Host = Email::host;  // Specify main and backup SMTP servers
  $mail->SMTPAuth = Email::SMTPAuth;                               // Enable SMTP authentication
  $mail->Username = Email::login;                 // SMTP username
  $mail->Password = Email::pw;                           // SMTP password
  $mail->SMTPSecure = Email::SMTPSecure;                            // Enable TLS encryption, `ssl` also accepted
  $mail->Port = Email::port;                                    // TCP port to connect to

  $mail->setFrom($from);
  $mail->addAddress($to);     // Add a recipient
  //$mail->addAddress('ellen@example.com');               // Name is optional
  //$mail->addReplyTo('info@example.com', 'Information');
  //$mail->addCC('cc@example.com');
  //$mail->addBCC('bcc@example.com');

  //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
  //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
  $mail->isHTML(true);                                  // Set email format to HTML

  $mail->Subject = $subject;
  $mail->Body    = $message;
  //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

  if(!$mail->send()) {
      echo 'Message could not be sent.';
      echo 'Mailer Error: ' . $mail->ErrorInfo;
  } else {
      echo 'Message has been sent';
  }
}


?>
