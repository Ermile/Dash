<?php
namespace lib;
/**
 * some check for baby to not harm yourself
 */
class baby
{

	public static function check($_txt)
	{
		if(!$_txt && isset($_SERVER['REQUEST_URI']))
		{
			$_txt = $_SERVER['REQUEST_URI'];
		}
		// decode url
		$_txt = urldecode($_txt);

		// check for problem in hex
		self::hex($_txt);
		// check for problem for containing forbidden chars
		self::is_forbidden_char($_txt);
	}



	/**
	 * check for using forbiden char in txt
	 * @param  [type]  $_txt            [description]
	 * @param  [type]  $_forbiddenChars [description]
	 * @return boolean                  [description]
	 */
	public static function is_forbidden_char($_txt, $_forbiddenChars = null)
	{
		if(!$_forbiddenChars || !is_array($_forbiddenChars))
		{
			$_forbiddenChars = ['"', "`" , "'", ';', ','];

		}
		foreach ($_forbiddenChars as $name)
		{
			if (stripos($_txt, $name) !== FALSE)
			{
				\lib\error::page('Hi Baby!');
			}
		}
	}


	public static function hex($_txt)
	{
		if(preg_match("#0x#Ui", $_txt))
		{
			\lib\error::page('OHH Baby!!');
		}
		if(preg_match("#0x#", $_txt))
		{
			\lib\error::page('OHH Baby!');
		}
	}


}
?>