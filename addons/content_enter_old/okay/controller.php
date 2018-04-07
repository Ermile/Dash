<?php
namespace content_enter\okay;


class controller extends \addons\content_enter\main\controller
{
	public function ready()
	{
		// if this step is locked go to error page and return
		if(self::lock('okay'))
		{
			self::error_page('okay');
			return;
		}

	}
}
?>