<?php
namespace content_account\my\username;


class model
{


	public static function getPost()
	{
		$post =
		[
			'username'    => \dash\request::post('username'),
		];

		return $post;
	}


	/**
	 * Posts a user add.
	 */
	public static function post()
	{

		$request = self::getPost();

		// ready request
		$id = \dash\coding::encode(\dash\user::id());

		$result = \dash\app\user::edit($request, $id);

		if(\dash\engine\process::status())
		{
			\dash\notif::clean();
			\dash\notif::ok(T_("Your username was changed"));
			\dash\log::set('editProfileUsername', ['newusername' => \dash\request::post('username'), 'code' => \dash\user::id()]);
			\dash\user::refresh();
			\dash\redirect::pwd();
		}
	}
}
?>