<?php
namespace dash\utility;
require(lib."utility/kavenegar_api.php");

/** call management class **/
class call
{
	/**
	 * call mobile
	 *
	 * @param      <type>     $_mobile    The mobile
	 * @param      <type>     $_template  The template
	 * @param      <type>     $_token     The token
	 * @param      array      $_options   The options
	 *
	 * @return     \|boolean  ( description_of_the_return_value )
	 */
	public static function send($_mobile, $_template, $_token, $_options = [])
	{
		if(!$_mobile || !$_template || !isset($_token))
		{
			return null;
		}

		$default_option =
		[
			'line'   => \dash\option::sms('kavenegar', 'line'),
			'token2' => null,
			'token3' => null,
			'type'   => 'call',
		];

		if(!is_array($_options))
		{
			$_options = [];
		}

		$_options = array_merge($default_option, $_options);

		// disable status
		// sms sevice is locked
		if(!\dash\option::sms('kavenegar', 'status'))
		{
			return false;
		}


		// cehck api key
		$api_key = \dash\option::sms('kavenegar','apikey');
		if(!$api_key)
		{
			return false;
		}

		$mobile = \dash\utility\filter::mobile($_mobile);
		if(!$mobile)
		{
			return false;
		}

		if(\dash\option::sms('kavenegar', 'iran') && substr($mobile, 0, 2) !== '98')
		{
			return false;
		}

		// function verify($_mobile, $_token, $_token2 = null, $_token3 = null, $_template = null, $_type = 'sms')
		$api    = new \dash\utility\kavenegar_api($api_key, $_options['line']);
		$result = $api->verify($mobile, $_token, $_options['token2'], $_options['token3'], $_template, $_options['type']);
		return $result;
	}
}
?>