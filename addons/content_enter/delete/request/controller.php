<?php
namespace addons\content_enter\delete\request;

class controller extends \addons\content_enter\main\controller
{
	public function ready()
	{
		// if this step is locked go to error page and return
		if(\dash\utility\enter::lock('delete/request'))
		{
			\dash\header::status(404, 'delete/request');
			return;
		}

		$this->get()->ALL('delete/request');
		$this->post('delete')->ALL('delete/request');
	}
}
?>