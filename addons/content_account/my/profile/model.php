<?php
namespace content_account\my\profile;


class model
{

	public static function getPost()
	{
		$post =
		[
			'firstname'   => \dash\request::post('firstname'),
			'lastname'    => \dash\request::post('lastname'),
			'bio'         => \dash\request::post('bio'),
			'displayname' => \dash\request::post('displayname'),
			'birthday'    => \dash\request::post('birthday'),
			'gender'      => \dash\request::post('gender'),

		];

		return $post;
	}



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
			\dash\log::set('editProfile', ['code' => \dash\user::id()]);
			\dash\user::refresh();
			\dash\notif::direct();
			\dash\redirect::pwd();
		}
	}
}
?>