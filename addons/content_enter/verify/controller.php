<?php
namespace addons\content_enter\verify;


class controller extends \addons\content_enter\main\controller
{
	public function ready()
	{
		// if the user is login redirect to base
		parent::if_login_not_route();

		// if this step is locked go to error page and return
		if(\dash\utility\enter::lock('verify'))
		{
			\dash\header::status(404, 'verify');
			return;
		}

		$this->get(false, 'verify_way')->ALL();
		$this->post('verify_way')->ALL();
	}
}
?>