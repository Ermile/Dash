<?php
namespace content_crm\member\notif;


class model
{
	public static function post()
	{

		$user_id           = \dash\coding::decode(\dash\request::get('id'));
		$request           = [];
		$request['mytext'] = \dash\request::post('notif');
		$request['to']     = $user_id;
		$request['from']   = \dash\user::id();

		\dash\log::set('notif_text', $request);

		if(\dash\engine\process::status())
		{
			\dash\notif::ok(T_("Note saved"));
			\dash\redirect::pwd();
		}
	}
}
?>