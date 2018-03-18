<?php
namespace lib;

require (core.'addons/lib/PHPMailer/Exception.php');
require (core.'addons/lib/PHPMailer/PHPMailer.php');
// require (core.'addons/lib/PHPMailer/SMTP.php');


class mail
{

	public static function send($_args = [])
	{
		$senderName = T_(ucfirst(\lib\url::root()));

		$default_args =
		[
			'from'    => 'info@dash.ermile.com',
			'to'      => null,
			'subject' => null,
			'body'    => null,
			'altbody' => null,
			'is_html' => true,
			'debug'   => true,
		];

		if(!is_array($_args))
		{
			$_args = [];
		}
		$_args = array_merge($default_args, $_args);

		$mail = new \PHPMailer\PHPMailer\PHPMailer;

		//Set who the message is to be sent from
		$mail->setFrom($_args['from'], $senderName);
		$mail->addAddress($_args['to']);
		// $mail->isHTML($_args['is_html'] ? true : false);
		$mail->Subject = $_args['subject'];
		$mail->AltBody = $_args['altbody'];
		$mail->Body    = $_args['body'];

		//send the message, check for errors
		if (!$mail->send())
		{
			if($_args['debug'])
			{
				\lib\notif::error(T_("Mailer Error :error", ['error' => $mail->ErrorInfo]));
			}
		    return false;
		}
		else
		{
		    return true;
		}

		//Set an alternative reply-to address
		// $mail->addReplyTo('j.evazzadeh@example.com', 'First Last');
		//Set who the message is to be sent to
		// $mail->addAddress('j.evazzadeh@live.com', 'Javad');
		//Set the subject line
		//Read an HTML message body from an external file, convert referenced images to embedded,
		//convert HTML into a basic plain-text alternative body
		// $mail->msgHTML(file_get_contents('contents.html'), dirname(__FILE__));
		//Replace the plain text body with one created manually
		//Attach an image file
		// $mail->addAttachment('images/phpmailer_mini.png');


		//$mail->SMTPDebug = 3;                               // Enable verbose debug output

		// $mail->isSMTP();                                      // Set mailer to use SMTP
		// $mail->Host = 'smtp1.example.com;smtp2.example.com';  // Specify main and backup SMTP servers
		// $mail->SMTPAuth = true;                               // Enable SMTP authentication
		// $mail->Username = 'user@example.com';                 // SMTP username
		// $mail->Password = 'secret';                           // SMTP password
		// $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
		// $mail->Port = 587;                                    // TCP port to connect to

		// $mail->From = 'from@example.com';
		// $mail->FromName = 'Mailer';
		// $mail->addAddress('j.evazzadeh@live.com', 'Javad');     // Add a recipient
		// $mail->addAddress('ellen@example.com');               // Name is optional
		// $mail->addReplyTo('info@example.com', 'Information');
		// $mail->addCC('cc@example.com');
		// $mail->addBCC('bcc@example.com');

		// $mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
		// $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
		// $mail->isHTML(true);                                  // Set email format to HTML

		// $mail->Subject = 'Here is the subject';
		// $mail->Body    = 'This is the HTML message body <b>in bold!</b>';
		// $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

		// if(!$mail->send()) {
		//     echo 'Message could not be sent.';
		//     echo 'Mailer Error: ' . $mail->ErrorInfo;
		// } else {
		//     echo 'Message has been sent';
		// }

	}


	public static function send_new($_to, $_subject, $_msg)
	{
		$mail = new \PHPMailer\PHPMailer\PHPMailer;

		$mail->SMTPDebug = 2;

		//Set who the message is to be sent from
		// $mail->setFrom('info@'.\lib\url::domain(), T_(ucfirst(\lib\url::root())));
		$senderName = T_(ucfirst(\lib\url::root()));
		$mail->setFrom('info@ermile.com', $senderName, 0);

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