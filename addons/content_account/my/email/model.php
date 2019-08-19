<?php
namespace content_account\my\email;


class model
{


	public static function getPost()
	{
		$post =
		[
			'email'    => \dash\request::post('email'),
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
			\dash\notif::ok(T_("Your email was changed"));
			\dash\log::set('editProfileEmail', ['newemail' => \dash\request::post('email'), 'code' => \dash\user::id()]);
			\dash\user::refresh();
			// \dash\notif::direct();
			\dash\redirect::pwd();
		}
	}
}
?>