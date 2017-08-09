<?php
namespace addons\content_account\signup;
use \lib\utility;
use \lib\debug;

class model extends \mvc\model
{
	/**
	 * signup to system
	 * @return [type] [description]
	 */
	public function post_signup()
	{
		// get parameters and set to local variables
		$mymobile   = utility::post('mobile', 'filter');
		$mypass     = utility::post('password');

		if(!$mymobile)
		{
			\lib\debug::error(T_("Please enter a valid mobile phone number"));
			return ;
		}

		$passlen 	= strlen(trim($mypass));

		if($passlen < 5)
		{
			debug::error(T_("Password length must be over five characters!"));
			return ;
		}elseif($passlen > 100)
		{
			debug::error(T_("Password length must be less 50 characters!"));
			return ;
		}

		$myperm     = $this->option('account');
		if(!$myperm)
		{
			$myperm = 'NULL';
		}
		$user_id     = \lib\db\users::signup(['mobile' => $mymobile, 'password' =>  $mypass, 'permission' =>  $myperm, 'port' => 'site']);
		if($user_id)
		{
			// generate verification code
			// save in logs table
			// set SESSION verification_mobile
			$code = \lib\utility\filter::generate_verification_code($user_id, $mymobile);
			if($code)
			{
				$service_name = \lib\router::get_domain(count(\lib\router::get_domain(-1))-2);
				$request = [
					'mobile' 		=> $mymobile,
					'template' 		=> $service_name . '-' . \lib\define::get_language(),
					'token'			=> $code,
					'type'			=> 'call'
					];
					$users_count = \lib\db\users::get_count();
					if(is_int($users_count) && $users_count > 1000)
					{
						$request['template'] =  $service_name . '-' . $this->module() . '-' . \lib\define::get_language();
						$request['token2'] 	= $users_count;
					}
				\lib\utility\sms::send($request, 'verify');
				debug::true(T_("Register successfully"));
				$_SESSION['tmp']['verify_mobile'] = $mymobile;
				$_SESSION['tmp']['verify_mobile_time'] = time() + (5*60);
				$this->redirector()->set_url('verification');
			}
			else
			{
				debug::error(T_("Please contact to administrator!"));
			}
		}
		elseif($user_id === false)
		{
			debug::error(T_("Mobile number exist!"));
		}
		else
		{
			debug::error(T_("Please contact to administrator!"));
		}
	}
}
?>