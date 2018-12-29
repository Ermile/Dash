<?php
namespace content_crm\member\contact;


class model
{

	public static function post()
	{
		\dash\permission::access('cpUsersEdit');
		$post =
		[
			'phone'        => \dash\request::post('phone'),
			'mobile'       => \dash\request::post('mobile'),
			'email'        => \dash\request::post('email'),
		];

		\dash\app\user::edit($post, \dash\request::get('id'));

		if(\dash\engine\process::status())
		{
			\dash\redirect::pwd();
		}
	}
}
?>
