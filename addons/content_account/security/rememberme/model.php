<?php
namespace content_account\security\rememberme;


class model
{



	public static function getPost()
	{
		$post =
		[
			'forceremember' => \dash\request::post('forceremember'),
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
			\dash\log::set('editProfileSecurityRemember', ['code' => \dash\user::id()]);
			\dash\user::refresh();
			\dash\redirect::pwd();
		}
	}
}
?>