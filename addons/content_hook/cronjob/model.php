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

			default:
				return;
				break;
		}
	}

}
?>