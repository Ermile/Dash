<?php
namespace dash\utility\pay;


class verify
{
	public static function verify($_bank, $_args)
	{
		\dash\utility\pay\setting::set();

		if(is_callable(["\\dash\\utility\\pay\\api\\$_bank\\back", 'verify']))
		{
			("\\dash\\utility\\pay\\api\\$_bank\\back")::verify($_args);
			return;
		}
	}

}
?>