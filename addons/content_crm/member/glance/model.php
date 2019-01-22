<?php
namespace content_crm\member\glance;


class model
{

	public static function post()
	{
		$user_id = \dash\coding::decode(\dash\request::get('id'));

		if(\dash\request::post('deleteuser') === 'DeleteUserYN' && \dash\permission::supervisor())
		{
			$removed = \dash\app\user::delete_user($user_id);
			if($removed)
			{
				\dash\notif::ok(T_("User removed"));
				\dash\redirect::pwd();
			}
			return false;
		}


	}
}
?>