<?php
namespace content_crm\email\send;


class model
{
	public static function post()
	{
		$email_to = \dash\request::post('email');
		$msg      = \dash\request::post('msg');
		$settings =
		[
			'from'    => 'info@dash.ermile.com',
			'to'      => $email_to,
			'subject' => 'test5',
			'body'    => $msg,
			'altbody' => '123',
			'is_html' => true,
		];
		\dash\log::set('emailSend', ['to' => $email_to]);
		\dash\mail::send($settings);
	}
}
?>
