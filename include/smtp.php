<?php


$mail->Mailer = 'sendmail';


/** @var PHPMailer $mail */
//$mail->SMTPDebug = 3;
$mail->CharSet = 'utf-8';
$mail->Mailer = 'smtp';
$mail->Host = "email-smtp.us-east-1.amazonaws.com";
$mail->SMTPSecurity = 'tls';
$mail->Port = "587";
$mail->SMTPAuth = true;
$mail->Username = "---";
$mail->Password = "----";
$mail->XMailer = ' ';
$mail->SMTPKeepAlive = true;
