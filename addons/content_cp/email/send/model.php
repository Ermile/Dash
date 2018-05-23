<?php
namespace content_cp\email\send;


class model
{
	public static function post()
	{
		$email_to = \dash\request::post('email');
		$msg      = \dash\request::post('msg');

		\dash\mail::send_new2($email_to, 'test3', $msg);
	}
}
?>
