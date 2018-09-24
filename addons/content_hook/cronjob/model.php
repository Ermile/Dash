<?php
namespace content_hook\cronjob;


class model
{
	/**
	 * save cronjob form
	 */
	public static function post()
	{

		if(!\dash\option::config('cronjob','status'))
		{
			return;
		}

		$url = \dash\request::get('type');

		switch ($url)
		{
			case 'notification':
				\dash\app\sendnotification::send();
				break;

			case 'closesolved':
				$time_now    = date("i");
				// every 10 minuts
				if((is_string($time_now) && mb_strlen($time_now) === 2 && $time_now{1} == '0') || \dash\permission::supervisor())
				{
					\dash\db\comments::close_solved_ticket();
				}
				break;

			default:
				return;
				break;
		}
	}

}
?>