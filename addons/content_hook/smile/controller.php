<?php
namespace content_hook\smile;


class controller
{

	public static function routing()
	{
		// if(!\dash\user::id())
		// {
		// 	// logout sample
		// 	$result =
		// 	[
		// 		'okay'      => false,
		// 		'logoutTxt' => T_("Goodbye"),
		// 		'logoutUrl' => \dash\url::kingdom(). '/logout'
		// 		// 'logoutUrl' => \dash\url::kingdom(). '/logout?mobile='. \dash\user::mobile()
		// 	];
		// }
		// else
		// {
		// 	$result =
		// 	[
		// 		'okay'     => true,
		// 		'newNotif' => (bool)random_int(0, 1),
		// 	];
		// }
		$myResult = [];


		if(\dash\user::id())
		{
			$myResult =
			[
				'notifNew'   => (bool)random_int(0, 1),
				'notifCount' => random_int(1, 10),
			];

			// if before this notif icon is off
			if(\dash\request::post('notifOn') === 'true')
			{
				// notification icon is pulsing before this
			}
			else
			{
				// it new message for the first time
				if($myResult['notifNew'])
				{
					\dash\notif::info('You have new message!');
				}
			}
		}
		else
		{
			// logout sample
			$myResult =
			[
				'logout' =>
				[
					'txt' => T_("Goodbye"),
					'url' => \dash\url::kingdom(). '/logout'
				]
			];
		}


		// set result into notif
		\dash\notif::result($myResult);
		// get result of notif and send it
		$notifResult = \dash\notif::get();
		\dash\code::jsonBoom($notifResult);
	}
}
?>