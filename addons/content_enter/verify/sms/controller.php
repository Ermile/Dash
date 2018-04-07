<?php
namespace content_enter\verify\sms;


class controller extends \addons\content_enter\main\controller
{
	public function ready()
	{

		// if this step is locked go to error page and return
		if(\dash\utility\enter::lock('verify/sms'))
		{
			\dash\header::status(404, 'verify/sms');
			return;
		}

		$this->get()->ALL('verify/sms');
		$this->post('verify')->ALL('verify/sms');

	}
}
?>