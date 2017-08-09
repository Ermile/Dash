<?php
namespace addons\content_account\home;

class controller extends \mvc\controller
{
	/**
	 * check route of account
	 * @return [type] [description]
	 */
	function _route()
	{
		$mymodule = $this->module();
		$referer  = \lib\router::urlParser('referer', 'domain');
		$from     = isset($_SESSION['tmp']['verify_mobile_time']) ? $_SESSION['tmp']['verify_mobile_time'] : null;
		if(time() >= $from)
		{
			$from = null;
		}

		if(\lib\db\sessions::get_cookie() && !$this->login())
		{
			$user_id = \lib\db\sessions::get_user_id();
			if($user_id)
			{
				$get_user = \lib\db\users::get($user_id);
				$myfields =
				[
					'id',
					'user_displayname',
					'user_mobile',
					'user_meta',
					'user_status',
				];
				$this->model();
				$this->model()->remember_me($get_user, $myfields);
				$this->referer();
			}
		}

		$islogin  = $this->login();
		// set referrer in cookie
		if($referer !== Domain)
			\lib\utility\cookie::write('referer', $referer, 60*15);
		// check permission for changepass
		if($mymodule === 'changepass' && !$from && !$islogin)
			\lib\error::access(T_("you can't access to this page!"));

		switch ($mymodule)
		{
			case 'home':
				$this->redirector()->set_url("login")->redirect();
				break;


			case 'verification':
			case 'verificationsms':
				if(!$from)
					\lib\error::access(T_("you can't access to this page!"));
				$this->model_name   = '\addons\content_account\\'.$mymodule.'\model';
				$this->display_name = 'content_account\\'.$mymodule.'\display.html';
				$this->post($mymodule)->ALL($mymodule);
				$this->get()->ALL($mymodule);
				break;

			case 'signup':
				if($islogin)
				{
					\lib\debug::true(T_("you are logined to system!"));
					$this->referer();
				}
				return;
				/**

				 Fix it later, only access if posible
				 */

			case 'login':
			case 'recovery':
				if($islogin)
				{
					\lib\debug::true(T_("you are logined to system!"));
					$this->referer();
				}
			case 'changepass':
				$this->model_name   = '\addons\content_account\\'.$mymodule.'\model';
				$this->display_name = 'content_account\\'.$mymodule.'\display.html';
				$this->post($mymodule)->ALL($mymodule);
				$this->get()          ->ALL($mymodule);
				break;


			case 'smsdelivery':
			case 'smscallback':
				$uid = 201500001;
				if(\lib\utility::get('uid') == $uid || \lib\utility\cookie::read('uid') == $uid)
				{
					$this->model_name	= '\addons\content_account\sms\model';
					$this->display_name	= 'content_account\sms\display.html';
					$this->post($mymodule)->ALL($mymodule);
					$this->get($mymodule) ->ALL($mymodule);
				}
				else
					\lib\error::access("SMS");
				break;


			// logout user from system then redirect to ermile
			case 'logout':

				$this->model_name	= '\lib\mvc\model';
				$user_id = $this->login('id');
				$this->model()->put_logout();
				\lib\db\sessions::logout($user_id);
				$url = $this->url("root") . '/'. \lib\define::get_language();
				$url = trim($url, '/');
				$this->redirector($url)->redirect();
				break;


			default:
				\lib\error::page();
				break;
		}
		// $this->route_check_true = true;
	}

	public function referer($_args = [])
	{
		\lib\debug::msg('direct', true);
		$url = $this->url("root");
		if(\lib\router::$prefix_base)
		{
			$url .= '/'.\lib\router::$prefix_base;
		}

		if(\lib\utility::get('referer'))
		{
			$url .= '/referer?to=' . \lib\utility::get('referer');
			$this->redirector($url)->redirect();
		}
		elseif(\lib\option::config('account_status'))
		{
			$url = $this->url("root");
			$_redirect_sub = \lib\option::config('redirect');

			if($_redirect_sub !== 'home')
			{
				// if(\lib\option::config('fake_sub'))
				// {
				// 	echo $this->redirector()->set_subdomain()->set_url($_redirect_sub)->redirect();
				// }
				// else
				// {
				//
				// }

				$url .= '/'. $_redirect_sub;

				if(isset($_args['user_id']) && $_args['user_id'])
				{
					$user_language = \lib\db\users::get_language($_args['user_id']);
					if($user_language && \lib\utility\location\languages::check($user_language))
					{
						$url .= \lib\define::get_current_language_string($user_language);
					}

				}
				$this->redirector($url)->redirect();
			}
		}
		$this->redirector()->set_domain()->set_url()->redirect();
	}
}
?>