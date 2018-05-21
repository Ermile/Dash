<?php
namespace content_cp\comments\edit;

class model
{
	public static function post()
	{
		$status = \dash\request::post('status');

		if(!$status)
		{
			\dash\notif::error(T_("Invalid status"));
			return false;
		}

		$update =
		[
			'content' => \dash\request::post('content'),
			'author'  => \dash\request::post('author'),
			'email'   => \dash\request::post('email'),
			'status'  => \dash\request::post('status'),
		];

		$post_detail = \dash\db\comments::update($update, \dash\coding::decode(\dash\request::get('id')));

		if(\dash\engine\process::status())
		{
			\dash\redirect::pwd();
		}
	}
}
?>
