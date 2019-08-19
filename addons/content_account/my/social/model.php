<?php
namespace content_account\my\social;


class model
{

	public static function getPost()
	{
		$post =
		[

			'website'     => \dash\request::post('website'),
			'instagram'   => \dash\request::post('instagram'),
			'linkedin'    => \dash\request::post('linkedin'),
			'facebook'    => \dash\request::post('facebook'),
			'twitter'     => \dash\request::post('twitter'),
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
			\dash\notif::ok(T_("Your profile successfully updated"));
			\dash\log::set('editProfileSocial', ['code' => \dash\user::id()]);
			\dash\user::refresh();
			\dash\redirect::pwd();
		}
	}
}
?>