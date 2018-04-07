<?php
namespace content_enter\block;


class controller extends \addons\content_enter\main\controller
{
	public function ready()
	{
		// if this step is locked go to error page and return
		if(\dash\utility\enter::lock('block'))
		{
			\dash\header::status(404, 'block');
			return;
		}
	}
}
?>