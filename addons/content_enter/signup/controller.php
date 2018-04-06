<?php
namespace addons\content_enter\signup;

class controller extends \addons\content_enter\main\controller
{
	public function ready()
	{
		parent::if_login_not_route();

		if(\dash\request::get('referer') && \dash\request::get('referer') != '')
		{
			$_SESSION['enter_referer'] = \dash\request::get('referer');
		}

		if(self::get_request_method() === 'get')
		{
			$this->get(false, 'signup')->ALL();
		}
		elseif(self::get_request_method() === 'post')
		{
			$this->post('signup')->ALL();
		}
		else
		{
			self::error_method('home');
		}
	}
}
?>