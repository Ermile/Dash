<?php
namespace content_crm\users\set;


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
			'twostep'     => \dash\request::post('twostep'),
			'sidebar'     => \dash\request::post('sidebar'),
			'language'    => \dash\request::post('language'),
			'website'     => \dash\request::post('website'),
			'instagram'   => \dash\request::post('instagram'),
			'linkedin'    => \dash\request::post('linkedin'),
			'facebook'    => \dash\request::post('facebook'),
			'twitter'     => \dash\request::post('twitter'),
			'gmail'       => \dash\request::post('gmail'),
			'firstname'   => \dash\request::post('firstname'),
			'lastname'    => \dash\request::post('lastname'),
			'username'    => \dash\request::post('username'),
			'title'       => \dash\request::post('title'),
			'type'        => \dash\request::post('type'),
			'birthday'    => \dash\request::post('birthday'),
			'bio'         => \dash\request::post('bio'),
			'displayname' => \dash\request::post('displayname'),
			'fullname'    => \dash\request::post('fullname'),
			'mobile'      => \dash\request::post('mobile'),
			'gender'      => \dash\request::post('gender'),
			'status'      => \dash\request::post('status'),
			'permission'  => \dash\request::post('permission'),
			'email'       => \dash\request::post('email'),
		];

		if($post['permission'] === 'supervisor')
		{
			\dash\notif::error("Invlid permission name", 'permission');
			return false;
		}

		if($post['permission'] === '0')
		{
			$post['permission'] = null;
		}

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

		if(intval(\dash\coding::decode(\dash\request::get('id'))) === intval(\dash\user::id()))
		{
			if(isset($request['permission']) && \dash\user::detail('permission') === 'admin' && $request['permission'] !== 'admin')
			{
				unset($request['permission']);
			}
		}


		// ready request

		if(\dash\request::get('id'))
		{
			\dash\permission::access('cpUsersEdit');

			$id = \dash\request::get('id');
			$result = \dash\app\user::edit($request, $id);

			if(intval(\dash\coding::decode($request['id'])) === intval(\dash\user::id()))
			{
				\dash\notif::direct();
				\dash\redirect::pwd();
			}
		}
		else
		{

			\dash\permission::access('cpUsersAdd');

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