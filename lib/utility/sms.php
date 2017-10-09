<?php
namespace lib\utility;
require(lib."utility/kavenegar_api.php");

/** Sms management class **/
class sms
{
	/**
	 * Makes a message.
	 *
	 * @param      <type>  $_message  The message
	 *
	 * @return     string  ( description_of_the_return_value )
	 */
	private static function make_message($_message)
	{
		$_message = trim($_message);

		// create complete message
		$sms_header = trim(\lib\option::sms('kavenegar', 'header'));
		$sms_footer = trim(\lib\option::sms('kavenegar', 'footer'));

		$message  = $sms_header;
		$message .= "\n";
		$message .= $_message;
		$message .= "\n\n";
		$message .= $sms_footer;

		if(\lib\option::sms('kavenegar', 'one') && mb_strlen($message) > self::is_rtl($message, true))
		{
			// create complete message
			$message = $sms_header. "\n". $_message;
			if(\lib\option::sms('kavenegar', 'one') && mb_strlen($message) > self::is_rtl($message, true))
			{
				// create complete message
				$message = $_message;
			}
		}
		return $message;
	}

	/**
	 * send sms
	 *
	 * @param      <type>     $_mobile   The mobile
	 * @param      <type>     $_message  The message
	 * @param      array      $_options  The options
	 *
	 * @return     \|boolean  ( description_of_the_return_value )
	 */
	public static function send($_mobile, $_message, $_options = [])
	{
		if(!$_mobile || !$_message || !trim($_message))
		{
			return null;
		}
		// disable status
		// sms sevice is locked
		if(!\lib\option::sms('kavenegar', 'status'))
		{
			return false;
		}

		// cehck api key
		$api_key = \lib\option::sms('kavenegar','apikey');
		if(!$api_key)
		{
			return false;
		}

		$default_option =
		[
			'line'           => \lib\option::sms('kavenegar', 'line'),
			'type'           => 1,
			'date'           => 0,
			'LocalMessageid' => null,
		];

		if(!is_array($_options))
		{
			$_options = [];
		}

		$_options = array_merge($default_option, $_options);


		$mobile = \lib\utility\filter::mobile($_mobile);
		if(!$mobile)
		{
			return false;
		}

		if(\lib\option::sms('kavenegar', 'iran') && substr($mobile, 0, 2) !== '98')
		{
			return false;
		}

		$message = self::make_message($_message);

		// send sms
		$api    = new \lib\utility\kavenegar_api($api_key, $_options['line']);
		$result = $api->send($mobile, $message, $_options['type'], $_options['date'], $_options['LocalMessageid']);
		return $result;

	}

	/**
	 * check the input is rtl or not
	 * @param  [type]  $string [description]
	 * @param  [type]  $type   [description]
	 * @return boolean         [description]
	 */
	private static function is_rtl($_str, $_type = false)
	{
		$rtl_chars_pattern = '/[\x{0590}-\x{05ff}\x{0600}-\x{06ff}]/u';
		$result            = preg_match($rtl_chars_pattern, $_str);
		if($_type)
		{
			$result = $result? 70: 160;
		}
		return $result;
	}


	/**
	 * Sends an array.
	 * send one message to multi mobile
	 *
	 * @param      <type>     $_mobiles  The mobiles
	 * @param      <type>     $_message  The message
	 * @param      array      $_options  The options
	 *
	 * @return     \|boolean  ( description_of_the_return_value )
	 */
	public static function send_array($_mobiles, $_message, $_options = [])
	{
		if(!$_mobiles || !$_message || !is_array($_mobiles))
		{
			return null;
		}

		// disable status
		// sms sevice is locked
		if(!\lib\option::sms('kavenegar', 'status'))
		{
			return false;
		}

		// cehck api key
		$api_key = \lib\option::sms('kavenegar','apikey');
		if(!$api_key)
		{
			return false;
		}

		$default_option =
		[
			'line'           => \lib\option::sms('kavenegar', 'line'),
			'type'           => 1,
			'date'           => 0,
		];

		if(!is_array($_options))
		{
			$_options = [];
		}

		$_options = array_merge($default_option, $_options);

		$accepted_mobile = [];
		foreach ($_mobiles as $key => $value)
		{
			$mobile = \lib\utility\filter::mobile($value);

			if($mobile)
			{
				if(\lib\option::sms('kavenegar', 'iran') && substr($mobile, 0, 2) !== '98')
				{
					continue;
				}
				array_push($accepted_mobile, $value);
			}
		}

		$accepted_mobile = array_filter($accepted_mobile);
		$accepted_mobile = array_unique($accepted_mobile);

		if(empty($accepted_mobile))
		{
			return null;
		}

		$result  = [];
		$message = self::make_message($_message);
		$api     = new \lib\utility\kavenegar_api($api_key, $_options['line']);
		$chunk   = array_chunk($accepted_mobile, 200);

		foreach ($chunk as $key => $last_200_mobile)
		{
			$result[] = $api->sendarray($_options['line'], $last_200_mobile, $message, $_options['type'], $_options['date']);
		}

		return $result;
	}
}
?>