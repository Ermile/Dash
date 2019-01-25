<?php
namespace content_hook\smile;


class controller
{

	public static function routing()
	{
		\dash\temp::set('force_stop_visitor', true);

		// in developer mode
		if(\dash\option::config('killSmile'))
		{
			\dash\code::boom();
			return;
		}

		$myResult  = [];
		$alertyOpt =
		[
			'alerty'            => true,
			'timeout'           => 2000,
			'showConfirmButton' => false
		];

		if(\dash\user::id())
		{
			$notifCount = \dash\app\log::my_notif_count();

			$myResult =
			[
				'notifNew'   => $notifCount ? true : false,
				'notifCount' => $notifCount,
			];
			// show alert as toast
			$alertyOpt['toast']    = true;
			$alertyOpt['position'] = 'top-end';


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
					\dash\notif::ok(T_("You have new message!"), $alertyOpt);
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