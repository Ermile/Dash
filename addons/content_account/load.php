<?php
namespace content_account;


class load
{

	public static function me()
	{

		$id = \dash\user::id();

		if(!$id)
		{
			\dash\header::status(404, T_("Invalid user id"));
		}

		$user_detail = \dash\db\users::get_by_id($id);

		if(!$user_detail)
		{
			\dash\header::status(404, T_("User id not found"));
		}

		\dash\data::dataRow(\dash\app\user::ready($user_detail, true));
	}
}
?>
