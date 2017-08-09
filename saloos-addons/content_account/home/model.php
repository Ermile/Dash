<?php
namespace addons\content_account\home;

class model extends \mvc\model
{
	function __construct($object = false)
	{
		parent::__construct($object);
		// $settings = $this->option('account', null, false, $this);
		$mymodule = $this->module();
		$isValid  = false;

		// // entire account part is disabled
		// if(isset($settings['status']) && !$settings['status'])
		// {
		// 	\lib\error::core('Disabled!');
		// }

		// check access permission to account
		// if user set passphrase for enter account
		if(\lib\option::config('passphrase') && $mymodule !=='logout')
		{
			// if user set pass key
			if(\lib\option::config('passkey'))
			{
				// get pass key and save it in myphrase variable
				$myPassKey   = \lib\option::config('passkey');
				$myPassValue = \lib\utility::get($myPassKey);
				// if user not set pass value in get, then check cookie for it
				if($myPassValue === null)
				{
					$myPassValue = \lib\utility\cookie::read($myPassKey);
				}

				// if not set this passkey and incorrect
				if($myPassValue === null)
				{
					$isValid = false;
				}
				// elseif set, compare value of it with settings
				elseif(\lib\option::config('passvalue'))
				{
					// passvalue exist and equal
					if(\lib\option::config('passvalue') === $myPassValue)
					{
						$isValid = true;
					}
					// passphrase exist but it not equal
					else
					{
						$isValid = false;
					}
				}
				// in this condition, user set fix key and value is not set
				// because in db name is set empty
				else
				{
					$isValid = true;
				}

				// if can access set cookie
				if($isValid)
				{
					\lib\utility\cookie::write($myPassKey, $myPassValue, 60*60*24*7); // allow 1week
				}
				// else disable access to this page
				else
				{
					\lib\utility\cookie::delete($myPassKey);
					\lib\error::login();
				}
			}
		}
	}

	public function remember_me($_tmp_result, $_myfields)
	{
		$this->setLoginSession($_tmp_result, $_myfields);
	}
}
?>