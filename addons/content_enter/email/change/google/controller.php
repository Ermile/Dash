<?php
namespace content_enter\email\change\google;

class controller extends \addons\content_enter\main\controller
{
	public function ready()
	{
		$url = \dash\url::directory();
		// if(\dash\utility\enter::lock('email/change/google'))
		// {
		// 	\dash\header::status(404, 'email/change/google');
		// 	return;
		// }

		$this->get()->ALL('email/change/google');
		$this->post('change_google')->ALL('email/change/google');
	}
}
?>