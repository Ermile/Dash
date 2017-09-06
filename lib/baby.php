<?php
namespace lib;
/**
 * some check for baby to not allow to harm yourself
 * v1.1
 */
class baby
{
	private static $level;
	/**
	 * add function to full check url and all user parameters
	 * @return block baby if needed
	 */
	public static function block()
	{
		// if we dont have request url it was very mysterious, say Hi to hitler
		if(!isset($_SERVER['REQUEST_URI']))
		{
			\lib\error::bad('Hi Father!');
		}

		// check request uri
		$_txt = $_SERVER['REQUEST_URI'];
		if(self::check($_txt ,false))
		{
			$msg = 'Hi Baby'. str_repeat('!', self::$level);
			\lib\error::bad($msg);
		}
	}

	/**
	 * check input text to have problem with hex or invalid chars
	 * @param  [type]  $_txt       [description]
	 * @param  boolean $_onlyCheck [description]
	 * @return [type]              [description]
	 */
	public static function check($_txt)
	{
		// decode url
		$_txt = urldecode($_txt);
		// check for problem in hex
		if(self::hex($_txt))
		{
			return true;
		}
		// check for problem for containing forbidden chars
		if(self::forbidden($_txt))
		{
			return true;
		}

		return false;
	}


	/**
	 * check some problem on hexas input or someother things
	 * @param  [type] $_txt [description]
	 * @return [type]       [description]
	 */
	public static function hex($_txt)
	{
		if(preg_match("#0x#Ui", $_txt))
		{
			self::$level = 1;
			return true;
		}
		if(preg_match("#0x#", $_txt))
		{
			self::$level = 2;
			return true;
		}
		// if cant find something return false
		return false;
	}


	/**
	 * check for using forbiden char in txt
	 * @param  [type]  $_txt            [description]
	 * @param  [type]  $_forbiddenChars [description]
	 * @return boolean                  [description]
	 */
	public static function forbidden($_txt, $_forbiddenChars = null)
	{
		if(!$_forbiddenChars || !is_array($_forbiddenChars))
		{
			$_forbiddenChars = ['"', "`" , "'", ';', ',', '%', '*', '\\'];
		}
		foreach ($_forbiddenChars as $name)
		{
			if (stripos($_txt, $name) !== FALSE)
			{
				self::$level = 3;
				return true;
			}
		}
	}

}
?>