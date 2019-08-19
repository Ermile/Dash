<?php
namespace content_account\my\avatar;


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

		$post = [];

		if(\dash\request::post('remove') === 'avatar')
		{
			$post = [];
			$post['avatar'] = null;
		}
		else
		{

			$avatar = self::upload_avatar();

			if($avatar)
			{
				$post['avatar'] = $avatar;
			}
			else
			{
				\dash\notif::warn(T_("No file was received"));
				return false;
			}
		}


		return $post;
	}


	/**
	 * Posts a user add.
	 */
	public static function post()
	{

		$request = self::getPost();

		if($request === false)
		{
			return false;
		}

		// ready request
		$id = \dash\coding::encode(\dash\user::id());

		$result = \dash\app\user::edit($request, $id);

		if(\dash\engine\process::status())
		{
			\dash\user::refresh();
			\dash\notif::direct(true);
			\dash\log::set('editProfileAvatar', ['code' => \dash\user::id()]);
			\dash\redirect::pwd();
		}
	}
}
?>