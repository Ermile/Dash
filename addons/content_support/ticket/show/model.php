<?php
namespace content_support\ticket\show;

class model
{

	public static function post()
	{
		// ready to insert comments
		$args =
		[
			'author'  => \dash\user::detail('displayname'),
			'email'   => \dash\user::detail('email'),
			'type'    => 'ticket',
			'content' => \dash\request::post('desc'),
			'title'   => \dash\request::post('title'),
			'mobile'  => \dash\user::detail("mobile"),
			'user_id' => \dash\user::id(),
			'parent'  => \dash\request::get('id'),
		];

		// insert comments
		$result = \dash\app\comment::add($args);

		if($result)
		{
			\dash\notif::ok(T_("Your ticket was sended"));
			\dash\redirect::pwd();
		}
	}
}
?>