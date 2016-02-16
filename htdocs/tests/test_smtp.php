<?php

if(!defined('ROOT')) {
	define('ROOT', dirname(__DIR__) . '/../');
}
require ROOT . 'include/conf.php';


$mail = new PHPMailer();
$mail->Mailer = 'smtp';

//$mail->SMTPDebug = 3;
$mail->SMTPSecurity='tls';
$mail->Host="smtp.gmail.com";
$mail->Port="587";
//$mail->Host = 'tls://smtp.gmail.com:587';

$mail->SMTPAuth=true;
$mail->Username="newsrtcouk@gmail.com";
$mail->Password="s4v4r|n4";

$mail->Encoding = 'quoted-printable';
$mail->CharSet = 'utf-8';
$mail->Hostname = $cfg->get('hostname');
$mail->Sender = $cfg->get('admin_email');
$mail->From = $cfg->get('admin_email');
$mail->FromName = $cfg->get('admin_from');
$mail->AddReplyTo($cfg->get('admin_email'), $cfg->get('admin_from'));

$mail->addAddress('viorel.irimia@gmail.com', 'Viorel Irimia');

$mail->Subject = 'testing...';
$mail->Body = 'testing this';
$mail->AltBody = 'testing this';
if(! $mail->Send()) {
	echo "Cannot send<br/>";
}
else {
	echo "Sent OK<br/>";
}


\Classes\Utils::pr($mail);