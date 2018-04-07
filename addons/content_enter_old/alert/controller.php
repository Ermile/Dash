<?php
namespace content_enter\alert;


class controller extends \addons\content_enter\main\controller
{
	public function ready()
	{
		// if this step is locked go to error page and return
		if(self::lock('alert'))
		{
			self::error_page('alert');
			return;
		}
	}
}
?>