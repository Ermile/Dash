<?php
namespace content_account\personalization\theme;


class model
{


	public static function getPost()
	{
		$post =
		[
			'theme'    => \dash\request::post('theme'),
		];

		if($post['theme'] === '0')
		{
			$post['theme'] = null;
		}

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
			\dash\notif::ok(T_("Your theme was changed"));
			\dash\log::set('editProfileUsername', ['newtheme' => \dash\request::post('theme'), 'code' => \dash\user::id()]);
			\dash\user::refresh();
			\dash\notif::direct();
			\dash\redirect::pwd();
		}
	}
}
?>