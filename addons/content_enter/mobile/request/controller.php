<?php
namespace addons\content_enter\mobile\request;

class controller extends \addons\content_enter\main\controller
{
	public function ready()
	{
			// if this step is locked go to error page and return
		if(\dash\utility\enter::lock('mobile/request'))
		{
			\dash\header::status(404, 'mobile/request');
			return;
		}

		// parent::ready();
		$this->get()->ALL('mobile/request');
		$this->post('mobile')->ALL('mobile/request');

	}
}
?>