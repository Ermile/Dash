<?php
namespace dash\social\telegram;

class log
{
	public static $logData = [];


	public static function sending($_method = null, $_sendData = null)
	{
		if(!isset(self::$logData['sendmethod']))
		{
			self::$logData['sendmethod']   = $_method;
			self::$logData['send']         = self::json($_sendData);
			self::$logData['senddate']     = date('Y-m-d H:i:s');
			self::$logData['sendtext']     = '';
			self::$logData['sendmesageid'] = '';
			self::$logData['sendkeyboard'] = '';
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
				self::$logData['meta'] .= "\n\n\n". self::json($_sendData);
			}
			else
			{
				self::$logData['meta'] = self::json($_sendData);
			}
		}
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
			'responsedate'  => date('Y-m-d H:i:s'),
			'url'           => tg::$api_token,
			// 'step'          => '',
			// 'meta'          => '',
			// 'status'        => '',
		];

		// combine collected and generated array together
		$myDetail = array_merge($myDetail, self::$logData);
		// save log into database
		\dash\db\telegrams::insert($myDetail);
	}









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