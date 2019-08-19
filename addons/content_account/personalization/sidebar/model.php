<?php
namespace content_account\personalization\sidebar;


class model
{


	public static function getPost()
	{
		$post =
		[
			'sidebar'    => \dash\request::post('sidebar'),
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
			\dash\notif::ok(T_("Your sidebar was changed"));
			\dash\log::set('editProfileUsername', ['newsidebar' => \dash\request::post('sidebar'), 'code' => \dash\user::id()]);
			\dash\user::refresh();
			\dash\notif::direct();
			\dash\redirect::pwd();
		}
	}
}
?>