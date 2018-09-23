<?php
namespace dash\social\telegram;

/** telegram execute last commits library**/
class exec
{
	/**
	 * this library send request to telegram servers
	 * v2.1
	 */


	/**
	 * Execute cURL call
	 * @return mixed Result of the cURL call
	 */
	public static function send($_method = null, $_data = null, $_jsonResult = false)
	{
		// if telegram is off then do not run
		if(!\dash\option::social('telegram', 'status'))
		{
			return T_('Telegram is off!');
		}
		// if method or data is not set return
		if(!$_method)
		{
			return T_('Method is not set!');
		}

		// if api key is not set get it from options
		if(!tg::$api_token)
		{
			tg::$api_token = \dash\option::social('telegram', 'token');
		}

		// if key is not correct return
		if(strlen(tg::$api_token) < 20)
		{
			return T_('Api key is not correct!');
		}
		// check user blocked us
		// if(\dash\app\tg\user::status() === 'block')
		// {
		// 	return T_('User is blocked us!');
		// }

		// check before execute
		$_data = exec_before::check($_method, $_data);

		// initialize curl
		$ch = curl_init();
		if ($ch === false)
		{
			return T_('Curl failed to initialize');
		}

		// log send this request
		log::sending($_method, $_data);
		// set some settings of curl
		$apiURL = "https://api.telegram.org/bot".tg::$api_token."/$_method";

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
			\dash\log::db('tg:error', ["meta" => curl_error($ch). ':'. curl_errno($ch)]);
			return curl_error($ch). ':'. curl_errno($ch);
		}
		if (empty($result) || is_null($result))
		{
			return T_('Empty server response');
		}
		curl_close($ch);
		if(substr($result, 0,1) === "{")
		{
			$result = json_decode($result, true);
		}
		// check final result and if have error try to do something
		if(isset($result['ok']) && $result['ok'] === false && isset($result['error_code']))
		{
			switch ($result['error_code'])
			{
				case 403:
					user::block();
					break;

				default:
					break;
			}
		}
		if($_jsonResult)
		{
			$result = json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
		}

		// Log curl response
		log::response($result);

		// return result
		return $result;
	}
}
?>