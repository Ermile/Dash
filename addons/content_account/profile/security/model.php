<?php
namespace content_account\profile\security;


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
			// 'twostep'       => \dash\request::post('twostep'),
			'forceremember' => \dash\request::post('forceremember'),

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
		if(!\dash\user::id())
		{
			return false;
		}

		if(\dash\request::post('type') === 'terminateall')
		{
			\dash\db\sessions::terminate_all_other(\dash\user::id());
			\dash\log::set('sessionTerminateAllOther');
			\dash\notif::ok(T_("All other session terminated"));
			\dash\redirect::pwd();
			return true;
		}

		if(\dash\request::post('type') === 'terminate' && \dash\request::post('id') && is_numeric(\dash\request::post('id')))
		{
			if(\dash\db\sessions::is_my_session(\dash\request::post('id'), \dash\user::id()))
			{
				\dash\log::set('sessionTerminate');
				\dash\db\sessions::terminate_id(\dash\request::post('id'));
				\dash\notif::ok(T_("Session terminated"));
				\dash\redirect::pwd();
				return true;
			}
		}

		$request = self::getPost();

		// ready request
		$id = \dash\coding::encode(\dash\user::id());

		$result = \dash\app\user::edit($request, $id);

		if(\dash\engine\process::status())
		{
			\dash\log::set('editProfileSecurity', ['code' => \dash\user::id()]);
			\dash\user::refresh();
			\dash\redirect::pwd();
		}
	}
}
?>