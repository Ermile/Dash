<?php
namespace dash\social\telegram;

class log
{
	public static $logData = [];


	/**
	 * save hook detail like time
	 * @return [type] [description]
	 */
	public static function hook()
	{
		self::$logData['hookdate'] = date('Y-m-d H:i:s');
	}


	/**
	 * log send request to telegram server into variable
	 * finish and insert into log db on calling done fn
	 * @param  [type] $_method   [description]
	 * @param  [type] $_sendData [description]
	 * @return [type]            [description]
	 */
	public static function sending($_method = null, $_sendData = null)
	{
		if(!isset(self::$logData['sendmethod']))
		{
			self::$logData['sendmethod']   = $_method;
			self::$logData['send']         = self::json($_sendData);
			self::$logData['senddate']     = date('Y-m-d H:i:s');
			self::$logData['sendmesageid'] = '';
			// save text of sended message
			if(isset($_sendData['text']))
			{
				self::$logData['sendtext'] = $_sendData['text'];
			}
			elseif(isset($_sendData['caption']))
			{
				self::$logData['sendtext'] = $_sendData['caption'];
			}
			// save keyboard sended seperately
			if(isset($_sendData['reply_markup']))
			{
				self::$logData['sendkeyboard'] = $_sendData['reply_markup'];
			}
		}
		elseif(!isset(self::$logData['send2']))
		{
			self::$logData['send2'] = self::json($_sendData);
		}
		elseif(!isset(self::$logData['send3']))
		{
			self::$logData['send3'] = self::json($_sendData);
		}
		else
		{
			// send in meta, because we send something before it
			if(isset(self::$logData['meta']))
			{
				self::$logData['meta'] .= "\n\n\n\n\n". self::json($_sendData);
			}
			else
			{
				self::$logData['meta'] = self::json($_sendData);
			}
		}

		// log::logy($_method. "\n");
		// log::logy(self::json($_sendData)."\n");
	}


	/**
	 * log of request responses from telegram server into variable
	 * finish and insert into log db on calling done fn
	 * @param  [type] $_response [description]
	 * @return [type]            [description]
	 */
	public static function response($_response = null)
	{
		if(!isset(self::$logData['response']))
		{
			self::$logData['response']     = self::json($_response);
			self::$logData['responsedate'] = date('Y-m-d H:i:s');
		}
		elseif(!isset(self::$logData['response2']))
		{
			self::$logData['response2'] = self::json($_response);
		}
		elseif(!isset(self::$logData['response3']))
		{
			self::$logData['response3'] = self::json($_response);
		}
		else
		{
			// send in meta, because we send something before it
			if(isset(self::$logData['meta']))
			{
				self::$logData['meta'] .= "\n". self::json($_response);
			}
			else
			{
				self::$logData['meta'] = self::json($_response);
			}
		}

		// log::logy(self::json($_response). "\n");
	}


	/**
	 * prepare array to save record of log in database
	 * @return function [description]
	 */
	public static function done()
	{
		$myDetail =
		[
			'chatid'        => hook::from(),
			'user_id'       => \dash\user::id(),
			'hook'          => self::json(tg::$hook),
			// 'hookdate'      => '',
			'hooktext'      => hook::text(),
			'hookmessageid' => hook::message_id(),
			// 'sendmethod'    => '',
			// 'send'          => '',
			// 'senddate'      => '',
			// 'sendtext'      => '',
			// 'sendmesageid'  => '',
			// 'sendkeyboard'  => '',
			// 'response'      => '',
			// 'responsedate'  => date('Y-m-d H:i:s'),
			'url'           => tg::$api_token,
			'step'          => step::current(),
			// 'meta'          => '',
			// 'status'        => '',
		];

		// combine collected and generated array together
		$myDetail = array_merge($myDetail, self::$logData);
		// save log into database
		\dash\db\telegrams::insert($myDetail);

		if(\dash\url::isLocal() || $chatID === 46898544 || $chatID === 344542267 || $chatID === 33263188)
		{
			\dash\code::jsonBoom(self::$logData, true);
		}
	}


	/**
	 * filter some array into json pretty to save on db and read better
	 * @param  [type] $_data [description]
	 * @return [type]        [description]
	 */
	public static function json($_data)
	{
		if(!$_data)
		{
			return null;
		}
		return json_encode($_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
	}


	public static function logy($_text)
	{
		if(!\dash\option::social('telegram', 'debug'))
		{
			return T_('debug mode is off!');
		}
		if(\dash\url::content() !== "hook")
		{
			return false;
		}

		$chatID = hook::chat();
		if(\dash\url::isLocal() || $chatID === 46898544 || $chatID === 344542267 || $chatID === 33263188)
		{
			echo $_text;
		}
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


}
?>