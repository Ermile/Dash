<?php
namespace content_account\profile;


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
			// 'twostep'     => \dash\request::post('twostep'),
			// 'sidebar'     => \dash\request::post('sidebar') ? true : false,
			// 'language'    => \dash\request::post('language'),
			// 'website'     => \dash\request::post('website'),
			// 'instagram'   => \dash\request::post('instagram'),
			// 'linkedin'    => \dash\request::post('linkedin'),
			// 'facebook'    => \dash\request::post('facebook'),
			// 'twitter'     => \dash\request::post('twitter'),
			'firstname'   => \dash\request::post('firstname'),
			'lastname'    => \dash\request::post('lastname'),
			'username'    => \dash\request::post('username'),
			'title'       => \dash\request::post('title'),
			'bio'         => \dash\request::post('bio'),
			'displayname' => \dash\request::post('displayname'),
			// 'birthday'    => \dash\request::post('birthday'),
			// 'fullname'    => \dash\request::post('fullname'),
			// 'gender'      => \dash\request::post('gender'),
			// 'email'       => \dash\request::post('email'),
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
			\dash\log::set('editProfile');
			\dash\user::refresh();
			\dash\redirect::pwd();
		}
	}
}
?>