<?php
namespace addons\content_account\signup;

class controller extends \addons\content_account\home\controller
{
	function _route()
	{
		$temp_login = false;
		if(isset($_SESSION['user']['mobile']) && substr($_SESSION['user']['mobile'], 0, 5) == 'temp_')
		{
			$temp_login = true;
		}
		if($this->login() && !$temp_login)
		{
			\lib\debug::true(T_("you are logined to system!"));
			$this->referer();
		}
		$canAccess = $this->option('account', 'meta', 'register');
		if($canAccess)
		{
			$this->post('signup')->ALL();
		}
		else
		{
			\lib\error::access(T_("Public registration is disabled!"));
		}
	}
}
?>