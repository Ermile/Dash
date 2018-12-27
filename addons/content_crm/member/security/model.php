<?php
namespace content_crm\member\security;


class model
{


	public static function getPost()
	{
		$post =
		[
			'twostep'       => \dash\request::post('twostep'),
			'forceremember' => \dash\request::post('forceremember'),
			'status'        => \dash\request::post('status'),
		];

		return $post;
	}


	/**
	 * Posts a user add.
	 */
	public static function post()
	{

		$user_id = \dash\coding::decode(\dash\request::get('id'));


		if(\dash\request::post('type') === 'terminate' && \dash\request::post('id') && is_numeric(\dash\request::post('id')))
		{
			if(\dash\db\sessions::is_my_session(\dash\request::post('id'), $user_id))
			{
				\dash\log::set('sessionTerminate');
				\dash\db\sessions::terminate_id(\dash\request::post('id'));
				\dash\notif::ok(T_("Session terminated"));
				\dash\redirect::pwd();
				return true;
			}
		}

		$request = self::getPost();

		$result = \dash\app\member::edit($request, \dash\request::get('id'));

		if(\dash\engine\process::status())
		{
			\dash\log::set('editProfileSecurity', ['code' => $user_id]);
			\dash\user::refresh();
			\dash\redirect::pwd();
		}
	}
}
?>