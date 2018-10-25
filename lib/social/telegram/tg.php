<?php
namespace dash\social\telegram;

/** telegram **/
class tg
{
	/**
	 * this library get and send telegram messages
	 * v21.0
	 */
	public static $api_token   = null;
	public static $name        = 'Ermile';
	public static $hook        = null;
	public static $AnswerOrder =
	[
		'dash:callback',
		'dash:ermile',
		'dash:utility',
		'dash:ticket',
		'dash:conversation',
		'dash:conversationFa',
	];
	public static $finish     = null;



	public static $language    = 'en_US';
	public static $fill        = null;
	public static $defaultText = 'Undefined';
	public static $defaultMenu = null;
	public static $saveDest    = root.'public_html/files/telegram/';


	/**
	 * fire telegram api and run hook to get all requests
	 * @return [type] [description]
	 */
	public static function fire()
	{
		// if telegram is off then do not run
		if(!\dash\option::social('telegram', 'status'))
		{
			return T_('Telegram is off!');
		}
		// disable visitor loger
		\dash\temp::set('force_stop_visitor', true);
		// session_destroy();
		self::fisher();
		// find answer for this message if need to answering
		answer::finder();
		// check notif and if exist send it
		notifer::check();
		// if we must pass result, we save it on result sending
		// now we need to save unanswered hook
		if(true)
		{
			// save log
			log::done();
		}
	}


	/**
	 * hook telegram messages. save and analyse user messages
	 * @param  boolean $_save [description]
	 * @return [type]         [description]
	 */
	public static function fisher()
	{
		// get hook and save in static variable
		self::$hook = json_decode(file_get_contents('php://input'), true);
		// save hook datetime
		log::hook();
		// force set session for this telegram user
		session::forceSet();
		// detect and set user id, access via \dash\user::id()
		user::detect();
		// check user lang and try to save language
		user::saveLanguage();
		// check if user send contact save user detail
		user::saveContact();
	}


	/**
	 * execute telegram method
	 * @param  [type] $_name [description]
	 * @param  [type] $_args [description]
	 * @return [type]        [description]
	 */
	static function __callStatic($_name, $_args)
	{
		// try to detect json output
		$jsonResult = false;
		if(substr($_name, 0, 5) === 'json_')
		{
			$_name = substr($_name, 5);
			$jsonResult = true;
		}
		if(isset($_args[0]))
		{
			$_args = $_args[0];
		}
		if($_name)
		{
			return exec::send($_name, $_args, $jsonResult);
		}
		return false;
	}


	public static function ok()
	{
		self::$finish = true;
	}


	public static function isOkay()
	{
		return self::$finish;
	}
}
?>