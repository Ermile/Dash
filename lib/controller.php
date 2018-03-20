<?php
namespace lib;


class controller
{
	public static function allow()
	{
		\lib\engine\main::allow(...func_get_args());
	}


	public static function allow_url()
	{
		\lib\engine\main::allow_url(...func_get_args());
	}
}
?>