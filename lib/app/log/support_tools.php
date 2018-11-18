<?php
namespace dash\app\log;

class support_tools
{
	private static $load = [];

	public static function load($_args)
	{
		if(empty(self::$load))
		{
			if(isset($_args['code']))
			{
				self::$load = \dash\db\comments::get(['id' => $_args['code'], 'limit' => 1]);
			}
		}

		return self::$load;
	}
}
?>