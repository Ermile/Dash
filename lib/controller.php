<?php
namespace lib;


class controller
{
	public static function allow()
	{
		\lib\engine\main::allow(...func_get_args());
	}
}
?>