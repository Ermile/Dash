<?php
namespace content_hook\smile;


class controller
{

	public static function routing()
	{
		$myResult  = [];
		$alertyOpt =
		[
			'alerty'            => true,
			'timeout'           => 2000,
			'showConfirmButton' => false
		];

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
					\dash\notif::ok('You have new message!', $alertyOpt);
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