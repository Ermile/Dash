<?php
namespace addons\content_enter\signup;

class controller extends \addons\content_enter\main\controller
{
	/**
	 * check route of account
	 * @return [type] [description]
	 */
	public function ready()
	{
		parent::if_login_not_route();

		if(\lib\utility::get('referer') && \lib\utility::get('referer') != '')
		{
			$_SESSION['enter_referer'] = \lib\utility::get('referer');
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