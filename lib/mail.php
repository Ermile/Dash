<?php
namespace lib;

require (core.'addons/lib/PHPMailer/Exception.php');
require (core.'addons/lib/PHPMailer/PHPMailer.php');
// require (core.'addons/lib/PHPMailer/SMTP.php');


class mail
{
	// use ;
	// use \PHPMailer\PHPMailer\Exception;


	public static function send($_to, $_subject, $_msg)
	{
		$mail = new \PHPMailer\PHPMailer\PHPMailer;

		$mail->SMTPDebug = 2;

		//Set who the message is to be sent from
		// $mail->setFrom('info@'.\lib\url::domain(), T_(ucfirst(\lib\url::root())));
		$senderName = T_(ucfirst(\lib\url::root()));
		$mail->setFrom('info@test.com', $senderName, 0);

		//Set who the message is to be sent to
		$mail->addAddress($_to);

		//Set the subject line
		$mail->Subject = $_subject;

		//Read an HTML message body from an external file, convert referenced images to embedded,
		//convert HTML into a basic plain-text alternative body
		$mail->msgHTML($_msg);

		//send the message, check for errors
		if (!$mail->send())
		{
			return $mail->ErrorInfo;
		}
		else
		{
			return true;
		}
	}
}
?>