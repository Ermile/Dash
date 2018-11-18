<?php
namespace content_account\smile;


class controller
{

	public static function routing()
	{
		if(!\dash\user::id())
		{
			// logout sample
			$result =
			[
				'ok'        => false,
				'logoutTxt' => T_("Goodbye"),
				'logoutUrl' => \dash\url::kingdom(). '/logout'
				// 'logoutUrl' => \dash\url::kingdom(). '/logout?mobile='. \dash\user::mobile()
			];
		}
		else
		{
			$result =
			[
				'ok'        => true,
				'newNotif'  => (bool)random_int(0, 1),
			];
		}


		\dash\code::jsonBoom($result);
	}
}
?>