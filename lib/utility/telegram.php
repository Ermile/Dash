<?php
namespace lib\utility;

class telegram
{

	/**
	 * the hasan service url
	 *
	 * @var        string
	 */
	private static $service_url                 = 'http://178.62.218.8:8081';

	/**
	 * save log if need
	 *
	 * @var        boolean
	 */
	public static $save_log = true;

	/**
	 * forse send by telegram service
	 * if this value is false the telegram request sended by hasan service
	 * if this value is true try to send telegram request by api.telegram.org
	 *
	 * @var        boolean
	 */
	public static $force_send_telegram_service = true;


	/**
	 * the telegram api url
	 *
	 * @var        string
	 */
	public static $telegram_api_url = 'https://api.telegram.org/bot';
	public static $bot_key          = null;


	/**
	 * sort telegram message and send
	 *
	 * @var        array
	 */
	public static $SORT = [];


	/**
	 * send array messages
	 *
	 * @param      array  $_args  The arguments
	 */
	public static function tg_curl_group($_args = [])
	{
		$default_args =
		[
			'url'      => self::$service_url,
			'app_name' => 'tejarak',
			'content'  => null,
		];

		if(is_array($_args))
		{
			$_args = array_merge($default_args, $_args);
		}
		else
		{
			$_args = $default_args;
		}

		if(!$_args['url'] || !$_args['content'] || !$_args['app_name'])
		{
			return false;
		}

		$url = "$_args[url]/$_args[app_name]/sendArray";

		$headers =
		[
			"content-type: application/json",
		];

		if(Tld === 'dev')
		{
			$temp_content = [];
			foreach ($_args['content'] as $key => $value)
			{
				if(isset($value['chat_id']))
				{
					if(in_array($value['chat_id'], [33263188, '33263188', 46898544, '46898544']))
					{
						$temp_content[] = $value;
					}
				}
			}

			if(empty($temp_content))
			{
				return false;
			}

			$_args['content'] = $temp_content;
		}

		$content = json_encode($_args['content'], JSON_UNESCAPED_UNICODE);

		self::curlExec($url, $headers, $content);
	}


	/**
	 * tg curl
	 *
	 * @param      array    $_args  The arguments
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public static function tg_curl($_args = [])
	{
		$default_args =
		[
			'url'      => self::$service_url,
			'app_name' => 'tejarak',
			'method'   => 'sendMessage',
			'text'     => null,
			'chat_id'  => null,
		];

		if(is_array($_args))
		{
			$_args = array_merge($default_args, $_args);
		}
		else
		{
			$_args = $default_args;
		}

		if(!$_args['url'] || !$_args['method'] || !$_args['chat_id'] || !$_args['app_name'])
		{
			return false;
		}

		if(Tld === 'dev')
		{
			if(!in_array($_args['chat_id'], [33263188, '33263188', 46898544, '46898544']))
			{
				return false;
			}
		}

		$url = "$_args[url]/$_args[app_name]/$_args[method]";

		$headers =
		[
			// "app-name: $_args[app_name]",
			"content-type: application/json",
			// "request-id: $_args[request_id]",
			// "request-method: $_args[method]",
			// "telegram-id: $_args[chat_id]",
		];

		if(!$_args['text'])
		{
			return false;
		}

		$content =
		[
			'method'  => $_args['method'],
			'text'    => $_args['text'],
			'chat_id' => $_args['chat_id'],
		];

		if(!$content['chat_id'])
		{
			return false;
		}

		$content = json_encode($content, JSON_UNESCAPED_UNICODE);

		self::curlExec($url, $headers, $content);
	}


	/**
	 * curl execut
	 *
	 * @param      <type>  $_url      The url
	 * @param      <type>  $_header   The header
	 * @param      <type>  $_content  The content
	 */
	private static function curlExec($_url, $_headers, $_content, $_option = [])
	{
		if(\lib\option::social('telegram', 'bot') && !self::$bot_key)
		{
			self::$telegram_api_url  .= \lib\option::social('telegram', 'bot');
		}
		elseif($bot_key)
		{
			self::$telegram_api_url  .= self::$bot_key;
		}
		else
		{
			return false;
		}

		if(!function_exists('curl_init'))
		{
			if(self::$save_log)
			{
				\lib\db\logs::set('telegram:curl:not:install', null, ['meta' =>[]]);
			}
			\lib\debug::warn(T_("Please install curl on your system"));
		}

		if(self::$force_send_telegram_service)
		{
			$array_content = json_decode($_content, true);
			if(preg_match("/sendArray/", $_url))
			{
				foreach ($array_content as $key => $value)
				{
					if(isset($value['method']))
					{
						$_url = self::$telegram_api_url . '/'. $value['method'];
						$handle   = curl_init();
						curl_setopt($handle, CURLOPT_URL, $_url);
						curl_setopt($handle, CURLOPT_HTTPHEADER, $_headers);
						curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
						curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
						curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
						curl_setopt($handle, CURLOPT_POST, true);
						curl_setopt($handle, CURLOPT_POSTFIELDS, json_encode($value, JSON_UNESCAPED_UNICODE));
						curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 10);
						curl_setopt($handle, CURLOPT_TIMEOUT, 20  );

						$response = curl_exec($handle);
						$mycode   = curl_getinfo($handle, CURLINFO_HTTP_CODE);

						curl_close ($handle);

						if(self::$save_log)
						{
							\lib\db\logs::set("telegram:service:curl", null, ['meta' => ['response' => $response, 'http_code' => $mycode, 'args' => func_get_args()]]);
						}
					}
				}
			}
			else
			{
				if(isset($array_content['method']))
				{
					$_url = self::$telegram_api_url . '/'. $array_content['method'];
					$handle   = curl_init();
					curl_setopt($handle, CURLOPT_URL, $_url);
					curl_setopt($handle, CURLOPT_HTTPHEADER, $_headers);
					curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
					curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
					curl_setopt($handle, CURLOPT_POST, true);
					curl_setopt($handle, CURLOPT_POSTFIELDS, $_content);
					curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 10);
					curl_setopt($handle, CURLOPT_TIMEOUT, 20  );

					$response = curl_exec($handle);
					$mycode   = curl_getinfo($handle, CURLINFO_HTTP_CODE);

					curl_close ($handle);

					if(self::$save_log)
					{
						\lib\db\logs::set("telegram:service:curl", null, ['meta' => ['response' => $response, 'http_code' => $mycode, 'args' => func_get_args()]]);
					}
				}
			}
		}
		else
		{
			$handle   = curl_init();
			curl_setopt($handle, CURLOPT_URL, $_url);
			curl_setopt($handle, CURLOPT_HTTPHEADER, $_headers);
			curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($handle, CURLOPT_POST, true);
			curl_setopt($handle, CURLOPT_POSTFIELDS, $_content);
			curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 10);
			curl_setopt($handle, CURLOPT_TIMEOUT, 20  );

			$response = curl_exec($handle);
			$mycode   = curl_getinfo($handle, CURLINFO_HTTP_CODE);

			curl_close ($handle);

			if(self::$save_log)
			{
				\lib\db\logs::set("telegram:curl", null, ['meta' => ['response' => $response, 'http_code' => $mycode, 'args' => func_get_args()]]);
			}
		}
	}


	/**
	 * Sends a message via telegram
	 *
	 * @param      <type>  $_chat_id  The chat identifier
	 * @param      <type>  $_text     The text
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function sendMessage($_chat_id, $_text, $_option = [])
	{
		if(!$_chat_id || !$_text)
		{
			return false;
		}

		$default_option =
		[
			'sort' => null,
		];

		if(is_array($_option))
		{
			$_option = array_merge($default_option, $_option);
		}

		$args =
		[
			'parse_mode' => 'html',
			'method'     => 'sendMessage',
			'chat_id'    => $_chat_id,
			'text'       => $_text,
		];

		if($_option['sort'])
		{
			self::$SORT[] = ['sort' => $_option['sort'], 'curl' => $args];
		}
		else
		{
			return self::tg_curl($args);
		}
	}



	/**
	 * Sends a message via telegram
	 *
	 * @param      <type>  $_chat_id  The chat identifier
	 * @param      <type>  $_text     The text
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function sendMessageGroup($_chat_id, $_text, $_option = [])
	{
		if(!$_chat_id || !$_text)
		{
			return false;
		}

		$default_option =
		[
			'sort' => null,
		];

		if(is_array($_option))
		{
			$_option = array_merge($default_option, $_option);
		}

		$args =
		[
			'parse_mode' => 'html',
			'method'     => 'sendMessage',
			'chat_id'    => $_chat_id,
			'text'       => $_text,
		];

		if($_option['sort'])
		{
			self::$SORT[] = ['sort' => $_option['sort'], 'curl' => $args];
		}
		else
		{
			return self::tg_curl($args);
		}
	}


	/**
	* send message as sort
	*/
	public static function sort_send()
	{

		if(!empty(self::$SORT))
		{
			$sort = array_column(self::$SORT, 'sort');
			array_multisort($sort,SORT_ASC, self::$SORT);
			self::$SORT = array_filter(self::$SORT);
			$curl_group = array_column(self::$SORT, 'curl');

			self::tg_curl_group(['content' => $curl_group]);

			// foreach (self::$SORT as $key => $value)
			// {
			// 	if(isset($value['curl']))
			// 	{
			// 		self::tg_curl($value['curl']);
			// 	}
			// }
		}
	}

	/**
	* clear cashed meessage
	*/
	public static function clean_cash()
	{
		self::$SORT = [];
	}
}
?>