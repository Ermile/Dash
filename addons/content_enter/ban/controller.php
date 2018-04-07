<?php
namespace addons\content_enter\ban;


class controller extends \addons\content_enter\main\controller
{
	public function ready()
	{
		// if this step is locked go to error page and return
		if(\dash\utility\enter::lock('ban'))
		{
			\dash\header::status(404, 'ban');
			return;
		}
	}
}
?>