<?php
namespace content_account\personalization\language;


class model
{


	public static function getPost()
	{
		$post =
		[
			'language'    => \dash\request::post('language'),
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
			\dash\notif::ok(T_("Your language was changed"));
			\dash\log::set('editProfileUsername', ['newlanguage' => \dash\request::post('language'), 'code' => \dash\user::id()]);
			\dash\user::refresh();
			// \dash\notif::direct();
			\dash\redirect::pwd();
		}
	}
}
?>