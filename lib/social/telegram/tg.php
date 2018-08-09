<?php
namespace dash\social\telegram;

/** telegram **/
class tg
{
	/**
	 * this library get and send telegram messages
	 * v21.0
	 */
	public static $api_key     = null;
	public static $name        = null;
	public static $language    = 'en_US';
	public static $cmd         = null;
	public static $cmdFolder   = null;
	public static $hook        = null;
	public static $fill        = null;
	public static $user_id     = null;
	public static $defaultText = 'Undefined';
	public static $defaultMenu = null;
	public static $saveDest    = root.'public_html/files/telegram/';
	public static $priority    =
	[
		'handle',
		'callback',
		'user',
		'menu',
		'simple',
		'conversation',
	];

	/**
	 * execute telegram method
	 * @param  [type] $_name [description]
	 * @param  [type] $_args [description]
	 * @return [type]        [description]
	 */
	static function __callStatic($_name, $_args)
	{
		if(isset($_args[0]))
		{
			$_args = $_args[0];
		}
		return exec::send($_name, $_args);
	}
}
?>