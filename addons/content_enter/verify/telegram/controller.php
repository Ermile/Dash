<?php
namespace content_enter\verify\telegram;


class controller extends \addons\content_enter\main\controller
{
	public function ready()
	{

		// if this step is locked go to error page and return
		if(\dash\utility\enter::lock('verify/telegram'))
		{
			\dash\header::status(404, 'verify/telegram');
			return;
		}


		$this->get()->ALL('verify/telegram');
		$this->post('verify')->ALL('verify/telegram');

	}
}
?>