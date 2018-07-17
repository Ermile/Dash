<?php
namespace dash;


class log
{

	public static function db($_caller, $_args = [])
	{
		return \dash\db\logs::set($_caller, \dash\user::id(), $_args);
	}
}
?>