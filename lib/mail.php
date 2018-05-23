<?php
namespace dash;

require (core.'addons/lib/PHPMailer/Exception.php');
require (core.'addons/lib/PHPMailer/PHPMailer.php');
// require (core.'addons/lib/PHPMailer/SMTP.php');


class mail
{
	public static function send($_args)
	{
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
			$senderName = T_(ucfirst(\dash\url::root()));

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
				\dash\notif::error(T_("Mailer Error :error", ['error' => $mail->ErrorInfo]));
			}
		    return false;
		}
		else
		{
		    return true;
		}


		// $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
		// try
		// {
		// 	// Server settings - smtp
		// 	// $mail->SMTPDebug = 2;                                 // Enable verbose debug output
		// 	// $mail->isSMTP();                                      // Set mailer to use SMTP
		// 	// $mail->Host = 'smtp1.example.com;smtp2.example.com';  // Specify main and backup SMTP servers
		// 	// $mail->SMTPAuth = true;                               // Enable SMTP authentication
		// 	// $mail->Username = 'user@example.com';                 // SMTP username
		// 	// $mail->Password = 'secret';                           // SMTP password
		// 	// $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
		// 	// $mail->Port = 587;                                    // TCP port to connect to

		// 	// use sendmail
		// 	// $mail->isSendmail();


		// 	//Recipients
		// 	$senderName = T_(ucfirst(\dash\url::root()));
		// 	$mail->setFrom('info@ermile.com', $senderName);
		// 	// $mail->setFrom('info@ermile.com', $senderName, 0);

		// 	//Set who the message is to be sent to
		// 	$mail->addAddress($_to);
		// 	// $mail->addAddress('joe@example.net', 'Joe User');     // Add a recipient

		// 	//Set the subject line
		// 	$mail->Subject = $_subject;

		// 	//Read an HTML message body from an external file, convert referenced images to embedded,
		// 	//convert HTML into a basic plain-text alternative body
		// 	$mail->msgHTML($_msg);

		// 	//Content
		// 	$mail->isHTML(true);                                  // Set email format to HTML
		// 	$mail->Subject = 'Here is the subject';
		// 	$mail->Body    = 'This is the HTML message body <b>in bold!</b>';
		// 	$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

		// 	$mail->send();
		// 	\dash\notif::info('Message has been sent');
		// }
		// catch (\Exception $e)
		// {
		// 	// \dash\notif::error('Message could not be sent. Mailer Error: ', $e->ErrorInfo);
		// 	\dash\notif::error(htmlspecialchars($e->getMessage()));
		// }
	}
}
?>