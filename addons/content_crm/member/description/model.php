<?php
namespace content_crm\member\description;


class model
{
	public static function post()
	{

		$user_id              = \dash\coding::decode(\dash\request::get('id'));
		$request              = [];
		$request['text']      = \dash\request::post('note');
		$request['user_id']   = $user_id;
		$request['creator']   = \dash\user::id();
		$request['status']    = 'enable';
		$request['subdomain'] = null;

		if(!trim($request['text']))
		{
			\dash\notif::error(T_("Please fill the box"), 'note');
			return false;
		}

		$check_duplicate = \dash\db\userdetail::get(['user_id' => $user_id, 'text' => $request['text'], 'limit' => 1]);
		if($check_duplicate)
		{
			\dash\notif::error(T_("Duplicate note for user founded"), 'note');
			return false;
		}

		\dash\db\userdetail::insert($request);

		if(\dash\engine\process::status())
		{
			\dash\notif::ok(T_("Note saved"));
			\dash\redirect::pwd();
		}
	}
}
?>