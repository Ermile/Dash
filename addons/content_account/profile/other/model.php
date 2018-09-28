<?php
namespace content_account\profile\other;


class model
{

	/**
	 * UploAads an avatar.
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public static function upload_avatar()
	{
		if(\dash\request::files('avatar'))
		{
			$uploaded_file = \dash\app\file::upload(['debug' => false, 'upload_name' => 'avatar']);

			if(isset($uploaded_file['url']))
			{
				\dash\notif::direct();

				return $uploaded_file['url'];
			}
			// if in upload have error return
			if(!\dash\engine\process::status())
			{
				return false;
			}
		}
		return null;
	}


	public static function getPost()
	{
		$post =
		[

			'sidebar'     => \dash\request::post('sidebar') ? true : false,
			'language'    => \dash\request::post('language'),
			'birthday'    => \dash\request::post('birthday'),
			'gender'      => \dash\request::post('gender'),

		];

		$avatar = self::upload_avatar();

		if($avatar)
		{
			$post['avatar'] = $avatar;
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
		$request['id'] = \dash\coding::encode(\dash\user::id());

		$result = \dash\app\user::edit($request);

		if(\dash\engine\process::status())
		{
			\dash\log::set('editProfileOther', ['code' => \dash\user::id()]);
			\dash\user::refresh();
			\dash\redirect::pwd();
		}
	}
}
?>