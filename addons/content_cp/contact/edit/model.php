<?php
namespace content_cp\contact\edit;

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

		if($update['content'])
		{
			$update['content'] = $_POST['content'];
		}

		$post_detail = \dash\app\comment::edit($update, \dash\request::get('id'));

		if(\dash\engine\process::status())
		{
			\dash\notif::ok(T_("Comment successfully updated"));
			\dash\redirect::pwd();
		}
	}
}
?>
