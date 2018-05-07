<?php
namespace content_cp\users\set;


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
			'mobile'      => \dash\request::post('mobile'),
			'displayname' => \dash\request::post('displayname'),
			'email'       => \dash\request::post('email'),
			'permission'  => \dash\request::post('permission'),
			'status'      => \dash\request::post('status'),
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

		$password   = \dash\request::post('password');
		$repassword = \dash\request::post('repassword');

		if($password)
		{
			if(!$repassword)
			{
				\dash\notif::error(T_("Please set repassword"), 'repassword');
				return false;
			}

			if($password !== $repassword)
			{
				\dash\notif::error(T_("Password not match whit repassword"), ['element' => ['password', 'repassword']]);
				return false;
			}

			$request['password'] = $password;

		}

		// ready request

		if(\dash\request::get('id'))
		{
			$request['id'] = \dash\request::get('id');
			$result = \dash\app\user::edit($request);
		}
		else
		{
			$result = \dash\app\user::add($request);
		}

		if(\dash\engine\process::status())
		{

			if(isset($result['user_id']))
			{
				\dash\redirect::to(\dash\url::here(). '/users/set?id='. $result['user_id']);
			}
			else
			{
				\dash\redirect::to(\dash\url::here(). '/users');
			}
		}
	}
}
?>