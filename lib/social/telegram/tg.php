<?php
namespace dash\social\telegram;

/** telegram **/
class tg
{
	/**
	 * this library get and send telegram messages
	 * v23.0
	 */
	public static $api_token   = null;
	public static $name        = 'ErmileBot';
	public static $hook        = null;
	public static $AnswerOrder =
	[
		'\lib\tg\detect',
		'dash:ermileInline',
		'dash:ermile',
		'dash:news',
		'dash:utility',
		'dash:ticket',
		'dash:hafez',
		'dash:conversation',
		'dash:conversationFa',
	];
	private static $finish     = null;


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
		// set bot name
		$myName = \dash\option::social('telegram', 'bot');
		if($myName)
		{
			self::$name = $myName;
		}
		// disable visitor loger
		\dash\temp::set('force_stop_visitor', true);
		// session_destroy();
		self::fisher();
		if(self::notLate())
		{
			// find answer for this message if need to answering
			answer::finder();
		}
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


	public static function isCallback()
	{
		if(isset(tg::$hook['callback_query']['data']))
		{
			return true;
		}
		return false;
	}


	public static function isInline()
	{
		if(isset(tg::$hook['inline_query']))
		{
			return true;
		}
		return false;
	}


	public static function isChosenInline()
	{
		if(isset(tg::$hook['chosen_inline_result']))
		{
			return true;
		}
		return false;
	}


	public static function isPrivate()
	{
		if(hook::chat('type') === 'private')
		{
			return true;
		}
		return false;
	}


	public static function website()
	{
		return \dash\url::base(). '/'. \dash\language::current();
	}


	public static function deepLink($_linkParam = null)
	{
		$deepLink = 'https://t.me/'. self::$name;
		$deepLink .= '?start='. $_linkParam;
		$deepLink .= '--lang='. \dash\app\tg\user::lang();

		return $deepLink;
	}


	public static function notLate()
	{
		if(self::isCallback())
		{
			return true;
		}
		if(self::isInline())
		{
			return true;
		}
		if(self::isChosenInline())
		{
			return true;
		}

		$msgDate = intval(hook::message('date'));
		// var_dump(date('Y-m-d H:i:s', $msgDate));
		if(!$msgDate)
		{
			// check on future
			return true;
		}

		$now      = new \DateTime();
		$now      = $now->getTimestamp();
		$dateDiff = intval(($now - $msgDate) / 60);
		// if message is sended more than 10 min age skip it
		if($dateDiff > 10)
		{
			$logDetail = $msgDate. '-'. $now. '>>'. $dateDiff.'Min';
			\dash\log::set('tg:outdateHook', ["meta" => $logDetail]);
			log::logy(T_('We are detect outdated message in hook.'). ' '. $logDetail);
			return false;
		}
		return true;
	}
}
?>