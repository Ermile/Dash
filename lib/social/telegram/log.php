<?php
namespace dash\social\telegram;

class log extends tg
{
	public static function save($_method = null, $_sendData = null, $_sendDate = null, $_response = null)
	{
		$myDetail =
		[
			// 'chatid'        => '',
			'user_id'       => \dash\user::id(),
			'hook'          => self::json(self::$hook),
			'hookdate'      => self::$hookDate,
			// 'hooktext'      => '',
			// 'hookmessageid' => '',
			'sendmethod'    => $_method,
			'send'          => self::json($_sendData),
			'senddate'      => $_sendDate,
			// 'sendtext'      => '',
			// 'sendmesageid'  => '',
			// 'sendkeyboard'  => '',
			'response'      => self::json($_response),
			'responsedate'  => date('Y-m-d H:i:s'),
			'url'           => self::$api_token,
			// 'step'          => '',
			// 'meta'          => '',
			// 'status'        => '',
		];

		\dash\db\telegrams::insert($myDetail);
	}

	public static function json($_data)
	{
		if(!$_data)
		{
			return null;
		}
		return json_encode($_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
	}


	public static function saveHook()
	{
		// try to register user if not exist
		\dash\social\telegram\user::handle();

		$myDetail =
		[
			// 'chatid'        => '',
			'user_id'       => \dash\user::id(),
			'hook'          => self::json(self::$hook),
			'hookdate'      => self::$hookDate,
			// 'hooktext'      => '',
			// 'hookmessageid' => '',
			// 'status'        => '',
		];
		\dash\db\telegrams::insert($myDetail);
	}




	/**
	 * save data on hooking
	 * @param  [type] $_data [description]
	 * @return [type]        [description]
	 */
	private static function saveHookOld($_data)
	{
		// define user detail array
		$from_id = self::response('from');
		// if we do not have from id return false
		if(!isset($_data['message']['from']) || !$from_id)
		{
			return false;
		}

		// catch user telegram from database and if not exist insert as new user
		self::catchTelegramUser($from_id, $_data['message']['from']);

		// // save user detail like contact or location if sended
		// if($contact = self::response('contact', null))
		// {
		// 	self::saveUserDetail('contact', $contact);
		// }
		// elseif($location = self::response('location'))
		// {
		// 	self::saveUserDetail('location', $location);
		// }



		// // change language if needede
		// if(\lib\router::get_storage('language') !== self::$language)
		// {
		// 	\lib\router::set_storage('language', self::$language );
		// 	// use saloos php gettext function
		// 	require_once(lib.'utility/gettext/gettext.inc');
		// 	// gettext setup
		// 	T_setlocale(LC_MESSAGES, \lib\router::get_storage('language'));
		// 	// Set the text domain as 'messages'
		// 	T_bindtextdomain('messages', root.'includes/languages');
		// 	T_bind_textdomain_codeset('messages', 'UTF-8');
		// 	T_textdomain('messages');
		// }
		return true;
	}


	/**
	 * save history of messages into session of this user
	 * @param  [type] $_text [description]
	 * @return [type]        [description]
	 */
	private static function saveHistory($_text, $_maxSize = 20)
	{
		if(!isset($_SESSION['tg']['history']))
		{
			$_SESSION['tg']['history'] = [];
		}
		// Prepend text to the beginning of an session array
		array_unshift($_SESSION['tg']['history'], $_text);
		// if count of messages is more than maxSize, remove old one
		if(count($_SESSION['tg']['history']) > $_maxSize)
		{
			// Pop the text off the end of array
			array_pop($_SESSION['tg']['history']);
		}
		// if last commit is repeated
		if(isset($_SESSION['tg']['history'][1]) &&
			$_SESSION['tg']['history'][1] === $_text || empty($_text)
		)
		{
			self::$skipText = true;
			return false;
		}
	}








	// \dash\db\telegrams::get(['key' => 'value', 'key2' => 'value2']);
    // \dash\db\telegrams::get(['key' => 'value', 'key2' => 'value2', 'limit' => 1]); // return array by size 1
    // \dash\db\telegrams::insert(['key' => 'value', 'key2' => 'value2']);
    // \dash\db\telegrams::update(['key' => 'value', 'key2' => 'value2'], 10); // where id = 10 update

}
?>