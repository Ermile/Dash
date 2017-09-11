<?php
namespace lib\mvc;
use \lib\router;
class controller extends \lib\controller
{
	use \lib\mvc\controllers\login;
	use \lib\mvc\controllers\sessions;
	use \lib\mvc\controllers\template;
	use \lib\mvc\controllers\tools;
	use \lib\mvc\controllers\url;
	use \lib\mvc\controllers\pagnation;
	use \lib\mvc\controllers\ref;


	/**
	 * [__construct description]
	 */
	public function __construct()
	{
		parent::__construct();

		if(MyAccount && SubDomain == null)
		{
			if(AccountService === Domain)
			{
				$domain = null;
			}
			else
			{
				$domain = AccountService.MainTld;
			}
			$param = $this->url('param');
			if($param)
			{
				$param = '?'.$param;
			}

			// if custom account exist, handle it, else use default login redirect process
			if(method_exists($this, 'handle_account_url'))
			{
				$this->handle_account_url($this->module(), $param, $domain);
			}
			else
			{
				switch ($this->module())
				{
					case 'signin':
					case 'login':
					case 'signup':
					case 'register':
						$this->redirector()->set_domain($domain)->set_url('enter'.$param)->redirect();
						break;

					case 'signout':
					case 'logout':
						// if(Domain !== MainService)
							// $this->redirector()->set_domain(MainService.'.'.Tld)->set_url('logout')->redirect();
						$this->redirector()->set_domain()->set_url('enter/logout'.$param)->redirect();
						break;

					// case 'favicon.ico':
					// 	$this->redirector()->set_domain()->set_url('static/images/favicon.png')->redirect();
					// 	break;
				}

				switch (\lib\router::get_url())
				{
					case 'account/recovery':
					case 'account/changepass':
					case 'account/verification':
					case 'account/verificationsms':
					case 'account/signin':
					case 'account/login':
					case 'account/signup':
					case 'account/register':
						$this->redirector()->set_domain($domain)->set_url('enter'.$param)->redirect();
						break;

					case 'account/logout':
					case 'account/signout':
						$this->redirector()->set_domain()->set_url('enter/logout'.$param)->redirect();
						break;
				}
			}

		}
		$myrep = router::get_repository_name();

		// running template base module for homepage
		if($myrep === 'content' && method_exists($this, 's_template_finder') && get_class($this) == 'content\home\controller')
		{
			$this->s_template_finder();
		}

		// pagnation config
		$this->pagnation_config();
		// save referer of users
		$this->save_ref();
		// check if isset remember me and login by this
		$this->check_remeber_login();
	}
}
?>