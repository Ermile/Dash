<?php
namespace dash\app\nationalcode;

class city
{
	public static function get($_code)
	{
		$addr = __DIR__. '/nationalcity.json';
		$get = [];
		if(is_file($addr))
		{
			$get = \dash\file::read($addr);
			$get = json_decode($get, true);
		}

		$_code = substr($_code, 0, 3);

		if(isset($get[$_code]))
		{
			return $get[$_code];
		}
		return null;
	}

}
?>