<?php
namespace addons\content_enter\main;

class view extends \mvc\view
{
	use _use;
	/**
	 * config
	 */
	public function config()
	{
		$this->include->css    = false;
		$this->include->js     = false;
		$this->data->bodyclass = 'unselectable enter';
		// $this->data->bodyclass .= ' bg'. rand(1, 15);
		$this->data->bodyclass .= ' bg'. date('g');

		// get mobile number to show in mobile input
		$session_mobile = self::get_enter_session('usernameormobile');
		$temp_mobile    = self::get_enter_session('temp_mobile');
		$myMobile       = null;
		if($this->login('mobile'))
		{
			$myMobile = $this->login('mobile');
		}
		elseif($session_mobile)
		{
			$myMobile = $session_mobile;
		}
		elseif($temp_mobile)
		{
			$myMobile = $temp_mobile;
		}
		// if mobile not set but the user was login
		// for example in pass/change page
		// get the user mobile from login.mobile

		// set mobile in display
		$this->data->get_mobile = $myMobile;
		$this->data->get_usernamemobile = $myMobile;


		// in all page the mobiel input is readonly
		$this->data->mobile_readonly = true;

		$this->data->google_login = \lib\option::social('google', 'status');

		if(\lib\router::get_sub_domain())
		{
			$this->data->google_login = false;
		}

	}
}
?>