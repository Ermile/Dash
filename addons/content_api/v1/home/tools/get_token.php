<?php
namespace addons\content_api\v1\home\tools;


trait get_token
{
	/**
	 * make token
	 *
	 * @return     array  ( description_of_the_return_value )
	 */
	public function token($_guest = false)
	{

		$guest_token = null;
		if(\lib\utility::request("guest"))
		{
			$guest_token = \lib\utility::request("guest");
		}

		$token = null;
		if($_guest)
		{
			$token = \addons\content_enter\main\tools\token::create_guest($this->authorization);
		}
		else
		{
			$token = \addons\content_enter\main\tools\token::create_tmp_login($this->authorization, $guest_token);
		}
		return ['token' => $token];
	}


	/**
	 * check verified temp token or no
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public function check_verify()
	{
		$temp_token = \lib\utility::request("temp_token");
		if(!$temp_token)
		{
			if(\lib\notif::$status)
			{
				\lib\notif::error(T_("Invalid parameter temp_token"), 'temp_token', 'arguments');
			}
			return false;
		}
		return \addons\content_enter\main\tools\token::check_verify($temp_token);
	}

}
?>