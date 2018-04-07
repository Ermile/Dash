<?php
namespace addons\content_enter\alert;


class controller extends \addons\content_enter\main\controller
{
	public function ready()
	{
		// if this step is locked go to error page and return
		if(\dash\utility\enter::lock('alert'))
		{
			\dash\header::status(404, 'alert');
			return;
		}
	}
}
?>