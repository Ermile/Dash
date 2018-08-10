<?php
namespace dash\social\telegram;

/** telegram execute last commits library**/
class exec extends tg
{
	/**
	 * this library send request to telegram servers
	 * v2.0
	 */


	/**
	 * Execute cURL call
	 * @return mixed Result of the cURL call
	 */
	protected static function send($_method = null, $_data = null, $_jsonResult = false)
	{
		// if telegram is off then do not run
		if(!\dash\option::social('telegram', 'status'))
		{
			return T_('telegram is off!');
		}
		// if method or data is not set return
		if(!$_method)
		{
			return T_('method is not set!');
		}

		// if api key is not set get it from options
		if(!self::$api_token)
		{
			self::$api_token = \dash\option::social('telegram', 'token');
		}

		// if key is not correct return
		if(strlen(self::$api_token) < 20)
		{
			return T_('api key is not correct!');
		}

		// initialize curl
		$ch = curl_init();
		if ($ch === false)
		{
			return T_('Curl failed to initialize');
		}

		// set some settings of curl
		$apiURL = "https://api.telegram.org/bot".self::$api_token."/$_method";
		$sendDate = date('Y-m-d H:i:s');
		curl_setopt($ch, CURLOPT_URL, $apiURL);
		// turn on some setting
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_SAFE_UPLOAD, true);
		// turn off some setting
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_HEADER, false);
		// timeout setting
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);

		if (!empty($_data))
		{
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: multipart/form-data'));
			curl_setopt($ch, CURLOPT_POSTFIELDS, $_data);
		}

		$result = curl_exec($ch);
		$mycode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if ($result === false)
		{
			return curl_error($ch). ':'. curl_errno($ch);
		}
		if (empty($result) || is_null($result))
		{
			return T_('Empty server response');
		}
		curl_close($ch);
		//Logging curl requests
		if(substr($result, 0,1) === "{")
		{
			$result = json_decode($result, true);
		}
		if($_jsonResult)
		{
			$result = json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
		}
		log::save($_method, $_data, $sendDate, $result);

		// return result
		return $result;
	}
}
?>