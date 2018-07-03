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
			if($mypath === 'all')
			{
				echo \dash\utility\twigTrans::extract('current', $myupdate);
				echo \dash\utility\twigTrans::extract('addons', $myupdate);
				\dash\code::boom();
			}
			else
			{
				echo \dash\utility\twigTrans::extract($mypath, $myupdate);
				\dash\code::boom();
			}
		}
	}

}
?>