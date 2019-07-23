<?php
namespace dash\social\telegram;

/** telegram execute last commits library**/
class exec
{
	/**
	 * this library send request to telegram servers
	 * v3.0
	 */
	private static $hit = 0;


	/**
	 * Execute cURL call
	 * @return mixed Result of the cURL call
	 */
	public static function send($_method = null, $_data = null, $_jsonResult = false)
	{
		// if telegram is off then do not run
		if(!\dash\option::social('telegram', 'status'))
		{
			\dash\log::set('tg:off');
			return T_('Telegram is off!');
		}
		$isTunnel = false;
		if(\dash\option::social('telegram', 'tunnel'))
		{
			$isTunnel = true;
		}
		// if method or data is not set return
		if(!$_method)
		{
			\dash\log::set('tg:method:empty');
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
			if($isTunnel)
			{
				// use tunnel default bot
			}
			else
			{
				\dash\log::set('tg:apikey:invalid');
				return T_('Api key is not correct!');
			}
		}
		// check user blocked us
		// if(\dash\app\tg\user::status() === 'block')
		// {
		// 	return T_('User is blocked us!');
		// }

		// plus plus counter
		self::$hit++;
		if(self::$hit > 20)
		{
			\dash\log::set('tg:hit20', ["meta" => 'hit'. self::$hit]);
			return T_('Maybe we have problem!');
		}
		else if(self::$hit > 10)
		{
			\dash\log::set('tg:hit10', ["meta" => 'hit'. self::$hit]);
		}

		// check before execute
		$_data = exec_before::check($_method, $_data);
		if(!$_data && !($_method === 'getWebhookInfo' || $_method === 'setWebhook'))
		{
			\dash\log::set('tg:exec:empty');
			return T_('Exec empty data');
		}
		$isJson = null;
		if($_method === 'answerInlineQuery')
		{
			$isJson = true;
		}

		// initialize curl
		$ch = \curl_init();
		if ($ch === false)
		{
			\dash\log::set('tg:curl:failed');
			return T_('Curl failed to initialize');
		}

		// log send this request
		log::sending($_method, $_data);
		// set some settings of curl
		$apiURL = "https://api.telegram.org/bot".tg::$api_token."/$_method";
		if($isTunnel)
		{
			$apiURL = "https://tunnel.ermile.com";
			// $apiURL = "https://tunnel.ermile.ir";
			$apiURL .= "?method=". $_method;
		}

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
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 7);
		curl_setopt($ch, CURLOPT_TIMEOUT, 7);

		$customHeader = [];
		// set token for tunner if exist
		if($isTunnel)
		{
			if(strlen(tg::$api_token) > 20)
			{
				array_push($customHeader, 'X-TG-TOKEN: '. tg::$api_token);
			}
		}
		if (!empty($_data))
		{
			if($isJson)
			{
				$dataJson = json_encode($_data);
				// set some extra header
				array_push($customHeader, 'Content-Type: application/json');
				array_push($customHeader, 'Content-Length: ' . strlen($dataJson));

				curl_setopt($ch, CURLOPT_POSTFIELDS, $dataJson);
			}
			else
			{
				curl_setopt( $ch, CURLOPT_POSTFIELDS, $_data);
				// set some extra header
				array_push($customHeader, 'Content-Type: multipart/form-data');
			}
		}
		// set custom header on all conditions
		curl_setopt($ch, CURLOPT_HTTPHEADER, $customHeader);

		$result = curl_exec($ch);
		$mycode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if ($result === false)
		{
			\dash\log::set('tg:error', ["meta" => curl_error($ch). ':'. curl_errno($ch)]);
			return curl_error($ch). ':'. curl_errno($ch);
		}
		if (empty($result) || is_null($result))
		{
			\dash\log::set('tg:response:empty');
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
					\dash\log::set('tg:user:block', ['meta' => $result['error_code']]);
					user::block();
					break;

				default:
					\dash\log::set('tg:user:error', ['meta' => $result['error_code']]);
					break;
			}
		}
		if($_jsonResult)
		{
			$result = json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
		}

		// Log curl response
		log::response($result);

		if(!tg::$hook)
		{
			// if it's not calling from hook save it
			log::done();
		}
		// return result
		return $result;
	}
}
?>