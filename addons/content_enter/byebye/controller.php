<?php
namespace content_enter\byebye;


class controller extends \addons\content_enter\main\controller
{
	public function ready()
	{
		// if this step is locked go to error page and return
		if(\dash\utility\enter::lock('byebye'))
		{
			\dash\header::status(404, 'byebye');
			return;
		}
		$this->get()->ALL('byebye');
	}
}
?>