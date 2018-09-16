<?php
namespace lib\telegram;

/** telegram execute last commits library**/
class exec extends tg
{
	/**
	 * this library send request to telegram servers
	 * v1.0
	 */


	/**
	 * Execute cURL call
	 * @return mixed Result of the cURL call
	 */
	public static function send($_method = null, array $_data = null, $_output = null)
	{
		// if telegram is off then do not run
		if(!\lib\utility\option::get('telegram', 'status'))
		{
			return 'telegram is off!';
		}
		// if method or data is not set return
		if(!$_method || !$_data)
		{
			return 'method or data is not set!';
		}

		// if api key is not set get it from options
		if(!self::$api_key)
		{
			self::$api_key = \lib\utility\option::get('telegram', 'meta', 'key');
		}
		// if key is not correct return
		if(strlen(self::$api_key) < 20)
		{
			return 'api key is not correct!';
		}

		// initialize curl
		$ch = curl_init();
		if ($ch === false)
		{
			return 'Curl failed to initialize';
		}

		$curlConfig =
		[
			CURLOPT_URL            => "https://api.telegram.org/bot".self::$api_key."/$_method",
			CURLOPT_POST           => true,
			CURLOPT_RETURNTRANSFER => true,
			// CURLOPT_HEADER         => true, // get header
			CURLOPT_SAFE_UPLOAD    => true,
			CURLOPT_SSL_VERIFYPEER => false,
		];
		curl_setopt_array($ch, $curlConfig);
		if (!empty($_data))
		{
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: multipart/form-data'));
			// curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
			// curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query($_data));
			curl_setopt( $ch, CURLOPT_POSTFIELDS, $_data);
		}
		if(Tld === 'dev')
		{
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		}

		$result = curl_exec($ch);
		if ($result === false)
		{
			return curl_error($ch). ':'. curl_errno($ch);
		}
		if (empty($result) | is_null($result))
		{
			return 'Empty server response';
		}
		curl_close($ch);
		//Logging curl requests
		if(substr($result, 0,1) === "{")
		{
			$result = json_decode($result, true);
			if($_output && isset($result[$_output]))
			{
				$result = $result[$_output];
			}
		}
		log::save($result);
		// return result
		return $result;
	}
}
?>