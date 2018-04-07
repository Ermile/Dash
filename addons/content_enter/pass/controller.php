<?php
namespace content_enter\pass;


class controller extends \addons\content_enter\main\controller
{
	public function ready()
	{
		// if this step is locked go to error page and return
		if(\dash\utility\enter::lock('pass'))
		{
			\dash\header::status(404, 'pass');
			return;
		}

		$this->post('check')->ALL('pass');

	}
}
?>