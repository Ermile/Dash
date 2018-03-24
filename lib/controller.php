<?php
namespace lib;


class controller
{
	public static function allow()
	{
		\lib\engine\mvc::allow(...func_get_args());
	}


	public static function allow_url()
	{
		\lib\engine\mvc::allow_url(...func_get_args());
	}
}
?>