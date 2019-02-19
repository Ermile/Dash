<?php
namespace content_api\v5\token;


class controller
{
	public static function routing()
	{
		\content_api\controller::check_authorization_v5();

		$result = \dash\app\user_auth::make();

		\dash\notif::result($result);

		\content_api\controller::end_api_v5();
	}
}
?>