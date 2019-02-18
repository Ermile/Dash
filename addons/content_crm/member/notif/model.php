<?php
namespace content_crm\member\notif;


class model
{
	public static function post()
	{

		$user_id                     = \dash\coding::decode(\dash\request::get('id'));
		$request                     = [];
		$request["notif_title"]      = \dash\request::post('title');
		$request["notif_small"]      = \dash\request::post('small');
		$request["notif_big"]        = \dash\request::post('big');
		$request["notif_sub_text"]   = \dash\request::post('sub_text');
		$request["notif_group"]      = \dash\request::post('group');
		$request["notif_large_icon"] = \dash\request::post('large_icon');
		$request["notif_icon"]       = \dash\request::post('icon');
		$request["notif_link"]       = \dash\request::post('link');
		$request["notif_external"]   = \dash\request::post('external') ? true : false;
		$request['to']               = $user_id;
		// $request['mycat']         = '';
		// $request['iconClass']     = '';
		// $request['icon']          = '';

		$request['from']       = \dash\user::id();

		\dash\log::set('notif_text', $request);

		if(\dash\engine\process::status())
		{
			\dash\notif::ok(T_("Note saved"));
			\dash\redirect::pwd();
		}
	}
}
?>