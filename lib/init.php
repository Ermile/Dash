<?php
namespace lib;
class init
{
	/**
	 * start init dash core and run first needle used in core
	 * @return [type] [description]
	 */
	public static function run()
	{
		// check min requirement to run dash core!
		self::minimum_requirement();
		// detect all content in this project
		\lib\content::initialize();
		// detect url and start work with them as first lib used by another one
		\lib\url::initialize();

	}


	/**
	 * check current version of server technologies like php and mysql
	 * and if is less than min, show error message
	 * @return [type] [description]
	 */
	public static function minimum_requirement()
	{
		// check php version to upper than 7.0
		if(version_compare(phpversion(), '7.0', '<'))
		{
			\lib\code::die("<p>For using Dash you must update php version to 7.0 or higher!</p>");
		}
	}
}
?>