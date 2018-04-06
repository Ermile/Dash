<?php
namespace dash\utility;

class enter
{

	public static function clean()
	{
		unset($_SESSION['enter']);
	}


	public static function set_session($_key, $_value)
	{
		if(!isset($_SESSION['enter']))
		{
			$_SESSION['enter'] = [];
		}

		$_SESSION['enter'][$_key] = $_value;
	}


	public static function get_session($_key)
	{
		if(isset($_SESSION['enter'][$_key]))
		{
			return $_SESSION['enter'][$_key];
		}
		return null;
	}
}
?>