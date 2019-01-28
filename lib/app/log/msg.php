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


	public static function is_me($_args)
	{
		if(isset($_args['to']) && isset($_args['from']) && \dash\user::id() && intval($_args['from']) === intval($_args['to']) && intval($_args['from']) === intval(\dash\user::id()))
		{
			return true;
		}

		return  false;
	}


	public static function displayname($_args)
	{
		$displayname = isset($_args['displayname']) ? $_args['displayname'] : null;
		return $displayname;
	}


	public static function mobile($_args)
	{
		$mobile = isset($_args['mobile']) ? $_args['mobile'] : null;
		return $mobile;
	}


	public static function avatar($_args)
	{
		$avatar = isset($_args['avatar']) ? $_args['avatar'] : null;
		return $avatar;
	}
}
?>