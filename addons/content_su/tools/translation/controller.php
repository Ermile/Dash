<?php
namespace content_su\tools\translation;

class controller
{
	public static function routing()
	{
		$mypath   = \dash\request::get('path');
		$myupdate = \dash\request::get('update');
		if($mypath)
		{
			echo \dash\utility\twigTrans::extract($mypath, $myupdate);
			\dash\code::exit();
		}
	}

}
?>