<?php
namespace dash\app\log;

class msg
{

	public static function myStripTags($_string)
	{
		if($_string)
		{
			$_string = str_replace("<br>", "\n", $_string);
			$_string = str_replace("<br/>", "\n", $_string);
			$_string = preg_replace("/\<\/[\w]\>/", ' ', $_string);
			$_string = strip_tags($_string);
			$_string = trim($_string);
		}
		return $_string;
	}
}
?>