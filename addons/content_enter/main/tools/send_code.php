<?php
namespace addons\content_enter\main\tools;
use \lib\utility;
use \lib\debug;

trait send_code
{

	/**
	 * return list of way we can send code to the user
	 *
	 * @param      <type>  $_mobile_or_email  The usernameormobile
	 *
	 * @return     array   ( description_of_the_return_value )
	 */
	public static function list_send_code_way($_mobile_or_email)
	{
		$i_can     = false;
		$is_mobile = false;
		$is_email  = false;

		if(\lib\utility\filter::mobile($_mobile_or_email))
		{
			$i_can     = true;
			$is_mobile = true;
		}
		// elseif(preg_match("/^(.*)\@(.*)\.(.*)$/", $_mobile_or_email))
		// {
		// 	$i_can    = true;
		// 	$is_email = true;
		// }

		if(!$i_can)
		{
			self::next_step('verify/what');
			self::go_to('verify/what');
		}

		$way = [];


		if($is_email)
		{
			// load email way
			array_push($way, 'email');
		}

		if($is_mobile)
		{
			if(self::user_data('chatid') && \lib\option::social('telegram', 'status'))
			{
				array_push($way, 'telegram');
			}

			if(self::user_data('mobile') && \lib\utility\filter::mobile(self::user_data('mobile')))
			{
				array_push($way, 'sms');
				array_push($way, 'call');
				array_push($way, 'sendsms');
			}
		}
		return $way;
	}


	/**
	 * Sends a code way.
	 */
	public static function send_code_way()
	{
		$host = Protocol."://" . \lib\router::get_domain();
		$host .= \lib\define::get_current_language_string();

		$host .= '/enter/verify/';
		self::open_lock('verify');


		self::go_redirect($host);
	}


	/**
	 * Sends a way.
	 * find send way
	 * @param      string  $_type  The type [ send_rate | resend_rate]
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function send_way($_type = 'send_rate')
	{
		// generate verify code to find what old way
		// if no code was set
		// make new code and way is null
		// we find the way is the first way to send
		self::generate_verification_code();
		// get the old way code
		$old_way = self::get_enter_session('verification_code_way');

		// get send rate by look at $_type
		if($_type == 'send_rate')
		{
			$rate = self::$send_rate;
		}
		elseif($_type == 'resend_rate')
		{
			$rate = self::$resend_rate;
		}
		else
		{
			$rate = self::$send_rate;
		}

		// first send way code
		if(!$old_way)
		{
			if(isset($rate[0]) && is_string($rate[0]))
			{
				if(self::get_enter_session('verification_code_id'))
				{
					if(\lib\db\logs::update(['desc' => $rate[0]], self::get_enter_session('verification_code_id')))
					{
						// update session on nex way
						self::set_enter_session('verification_code_way', $rate[0]);
						// first way to send code
						return $rate[0];
					}
				}
			}
		}

		// find key of this old way
		$current_key = array_search($old_way, $rate);
		// if the key is find
		if(isset($current_key))
		{
			// go to nex key
			$next_key = $current_key + 1;
			if(isset($rate[$next_key]) && is_string($rate[$next_key]))
			{
				// nex way
				if(self::get_enter_session('verification_code_id'))
				{
					// update log on next way
					if(\lib\db\logs::update(['desc' => $rate[$next_key]], self::get_enter_session('verification_code_id')))
					{
						// update session on nex way
						self::set_enter_session('verification_code_way', $rate[$next_key]);
						// return the way to got to this step
						return $rate[$next_key];
					}
				}
			}
		}
		return false;
	}


	/**
	 * Gets the last way.
	 * get last way of send rate
	 *
	 */
	public static function get_last_way($_type = 'send_rate')
	{
		// get the old way code
		$old_way = self::get_enter_session('verification_code_way');

		// get send rate by look at $_type
		if($_type == 'send_rate')
		{
			$rate = self::$send_rate;
		}
		elseif($_type == 'resend_rate')
		{
			$rate = self::$resend_rate;
		}
		else
		{
			$rate = self::$send_rate;
		}

		// first send way code
		if(!$old_way)
		{
			$old_way = ':/';
		}

		// find key of this old way
		$current_key = array_search($old_way, $rate);
		// if the key is find
		if(isset($current_key))
		{
			// go to nex key
			$next_key = $current_key - 1;
			if(isset($rate[$next_key]) && is_string($rate[$next_key]))
			{
				return $rate[$next_key];
			}
		}

		if(isset($rate[0]) && is_string($rate[0]))
		{
			return $rate[0];
		}
		return false;
	}
}
?>