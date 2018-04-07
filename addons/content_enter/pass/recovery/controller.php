<?php
namespace addons\content_enter\pass\recovery;

class controller extends \addons\content_enter\main\controller
{
	public function ready()
	{

		// if this step is locked go to error page and return
		if(\dash\utility\enter::lock('pass/recovery'))
		{
			\dash\header::status(404, 'pass/recovery');
			return;
		}

		// parent::ready();
		$this->get('pass')->ALL('pass/recovery');
		$this->post('pass')->ALL('pass/recovery');
	}
}
?>