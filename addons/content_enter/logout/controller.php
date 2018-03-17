<?php
namespace addons\content_enter\logout;

class controller extends \addons\content_enter\main\controller
{
	public function ready()
	{
		// if user login just can view this page
		self::if_login_route();
		// check request method
		if(self::get_request_method() === 'get')
		{
			// get user logout
			self::set_logout(\lib\user::id());
		}
		else
		{
			// make error method
			self::error_method('logout');
		}
	}
}
?>