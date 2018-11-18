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
		$myResult =
		[
			'okay'     => false,
		];


		if(\dash\user::id())
		{
			$myResult =
			[
				'okay'     => true,
				'newNotif' => (bool)random_int(0, 1),
			];
		}
		else
		{
			// logout sample
			$myResult =
			[
				'okay'      => false,
				'logoutTxt' => T_("Goodbye"),
				'logoutUrl' => \dash\url::kingdom(). '/logout'
				// 'logoutUrl' => \dash\url::kingdom(). '/logout?mobile='. \dash\user::mobile()
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