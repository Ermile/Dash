<?php
namespace content_enter\okay;


class controller extends \addons\content_enter\main\controller
{
	public function ready()
	{
		// if this step is locked go to error page and return
		if(\dash\utility\enter::lock('okay'))
		{
			\dash\header::status(404, 'okay');
			return;
		}

	}
}
?>