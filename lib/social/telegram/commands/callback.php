<?php
namespace dash\social\telegram\commands;
// use telegram class as bot
use \dash\social\telegram\tg as bot;

class callback
{
	/**
	 * execute user request and return best result
	 * @param  [type] $_cmd [description]
	 * @return [type]       [description]
	 */
	public static function run($_cmd)
	{
		$response = null;
		switch ($_cmd['command'])
		{
			case 'cb_go_right':
				$response = self::go_right();
				break;

			case 'cb_go_left':
				$response = self::go_left();
				break;

			default:
				break;
		}
		if($response)
		{
			if(!isset($result['show_alert']))
			{
				$response['show_alert'] = true;
			}
			bot::answerCallbackQuery($response);
			bot::ok();
		}
	}


	/**
	 *
	 * @return [type] [description]
	 */
	public static function go_right()
	{
		$result['text'] = 'hey right';
		return $result;
	}


	/**
	 *
	 * @return [type] [description]
	 */
	public static function go_left()
	{
		$result['text'] = 'hey left';
		return $result;
	}
}
?>