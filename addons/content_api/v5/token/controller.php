<?php
namespace content_api\v5\token;


class controller
{
	public static function routing()
	{
		\content_api\v5::check_authorization_v5();

		$x_app_request = \content_api\v5::$v5;

		if(isset($x_app_request['x_app_request']))
		{
			$x_app_request = $x_app_request['x_app_request'];
		}
		else
		{
			$x_app_request = null;
		}

		$result = \dash\app\user_auth::make(['gateway' => $x_app_request]);

		\dash\notif::result($result);

		\content_api\v5::end5();
	}
}
?>