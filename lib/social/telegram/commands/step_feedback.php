<?php
namespace dash\social\telegram\commands;
// use telegram class as bot
use \dash\social\telegram\tg as bot;
use \dash\social\telegram\step;
use \dash\social\telegram\hook;

class step_feedback
{
	private static $menu = ["remove_keyboard" => true];

	/**
	 * create define menu that allow user to select
	 * @param  boolean $_onlyMenu [description]
	 * @return [type]             [description]
	 */
	public static function start()
	{
		step::start('feedback');

		return self::step1();
	}


	/**
	 * show thanks message
	 * @return [type] [description]
	 */
	public static function step1()
	{
		// after this go to next step
		step::plus();
		// show give contact menu
		$menu     = self::$menu;
		$txt_text = "";

		$txt_text = T_("Thank you for choosing us.")."\n\n";
		$txt_text .= T_("Knowing your valuable comments about bugs and problems and more importantly your precious offers will help us in this way.")."\n\n";
		$txt_text .= T_("Please wrote your request, offer, criticism or appreciation");

		$result   =
		[
			'text'         => $txt_text,
			'reply_markup' => $menu,
		];

		bot::sendMessage($result);

		// return menu
		return $result;
	}



	public static function step2($_feedback)
	{
		$txt_text = T_("Your valuable feedback is saved.")."\n";
		$txt_text .= T_("Thank you;)");

		if(strlen($_feedback) < 10)
		{
			$txt_text = T_('Thanks')."!\n";
		}

		self::saveComment($_feedback);
		$result =
		[
			'text'         => $txt_text,
			'reply_markup' => step::get('menu'),
		];
		bot::sendMessage($result);

		step::stop();
		return $result;
	}


	/**
	 * save comment of this user into comments table
	 * @param  [type] $_feedback [description]
	 * @return [type]            [description]
	 */
	private static function saveComment($_feedback)
	{
		$meta =
		[
			'url' => 'telegram'
		];
		if(\dash\user::id())
		{
			$meta['user'] = \dash\user::id();
		}
		$result = \dash\db\comments::save($_feedback, $meta);

		// send feedback to javad account after saving in comments table
		$text   = "📨🔔 ". T_('New feedback')."\n";
		$text   .= '<b>'.hook::from('first_name'). ' '. hook::from('last_name'). "</b>\n";
		$text   .= '@'. hook::from('username'). "\n\n";
		$text   .= $_feedback. "\n\n";
		$text   .= T_("User_id"). ' '. hook::from(). "\n";
		$msg    =
		[
			'method'       => 'sendMessage',
			'text'         => $text,
			'chat_id'      => '46898544',

		];

		$result = bot::sendMessage($msg);


		return $result;
	}
}
?>