<?php
namespace dash\app\log;

class msg
{

	public static function myStripTags($_string)
	{
		if($_string)
		{
			$_string = str_replace('&nbsp;', ' ', $_string);
			$_string = str_replace("<br>", "\n", $_string);
			$_string = str_replace("<br/>", "\n", $_string);
			$_string = str_replace('</p>', "</p>\n", $_string);
			$_string = strip_tags($_string, '<b><i><a><code><pre>');
			$_string = trim($_string);
		}
		return $_string;
	}
}
?>