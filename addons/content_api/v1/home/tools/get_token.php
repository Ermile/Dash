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
		if(\dash\utility::request("guest"))
		{
			$guest_token = \dash\utility::request("guest");
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
		$temp_token = \dash\utility::request("temp_token");
		if(!$temp_token)
		{
			if(\dash\engine\process::status())
			{
				\dash\notif::error(T_("Invalid parameter temp_token"), 'temp_token', 'arguments');
			}
			return false;
		}
		return \addons\content_enter\main\tools\token::check_verify($temp_token);
	}

}
?>