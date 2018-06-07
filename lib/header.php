<?php
namespace dash;

class header
{
	private static $HEADER;

	/**
	* get header
	*/
	public static function get($_name = null)
	{
		if(!self::$HEADER)
		{
			$my_header = null;
			// get apache headers
			if(function_exists('apache_request_headers'))
			{
				$my_header = apache_request_headers();
			}
			else
			{
				$out = null;
				foreach($_SERVER as $key => $value)
		        {
		            if (substr($key,0,5)=="HTTP_")
		            {
		                $key = str_replace(" ","-", strtolower(str_replace("_"," ",substr($key,5))));
		                $out[$key] = $value;
		            }
		            else
		            {
		                $out[$key] = $value;
					}
		    	}
		    	$my_header = $out;
			}

			self::$HEADER = \dash\safe::safe($my_header);
		}

		if($_name)
		{
			if(array_key_exists($_name, self::$HEADER))
			{
				return self::$HEADER[$_name];
			}
			else
			{
				return null;
			}
		}
		else
		{
			return self::$HEADER;
		}
	}


	/**
	 * Retrieve the description for the HTTP status
	 * @param int $_code HTTP status code
	 * @return string Empty string if not found, or description if found
	 */
	public static function desc($_code)
	{
		$headers_list =
		[
			100 => T_('Continue'),
			101 => T_('Switching Protocols'),
			102 => T_('Processing'),

			200 => T_('OK'),
			201 => T_('Created'),
			202 => T_('Accepted'),
			203 => T_('Non-Authoritative Information'),
			204 => T_('No Content'),
			205 => T_('Reset Content'),
			206 => T_('Partial Content'),
			207 => T_('Multi-Status'),
			226 => T_('IM Used'),

			300 => T_('Multiple Choices'),
			301 => T_('Moved Permanently'),
			302 => T_('Found'),
			303 => T_('See Other'),
			304 => T_('Not Modified'),
			305 => T_('Use Proxy'),
			306 => T_('Reserved'),
			307 => T_('Temporary Redirect'),
			308 => T_('Permanent Redirect'),

			400 => T_('Bad Request'),
			401 => T_('Unauthorized'),
			402 => T_('Payment Required'),
			403 => T_('Forbidden'),
			404 => T_('Not Found'),
			405 => T_('Method Not Allowed'),
			406 => T_('Not Acceptable'),
			407 => T_('Proxy Authentication Required'),
			408 => T_('Request Timeout'),
			409 => T_('Conflict'),
			410 => T_('Gone'),
			411 => T_('Length Required'),
			412 => T_('Precondition Failed'),
			413 => T_('Request Entity Too Large'),
			414 => T_('Request-URI Too Long'),
			415 => T_('Unsupported Media Type'),
			416 => T_('Requested Range Not Satisfiable'),
			417 => T_('Expectation Failed'),
			418 => T_('I\'m a teapot'),
			421 => T_('Misdirected Request'),
			422 => T_('Unprocessable Entity'),
			423 => T_('Locked'),
			424 => T_('Failed Dependency'),
			426 => T_('Upgrade Required'),
			428 => T_('Precondition Required'),
			429 => T_('Too Many Requests'),
			431 => T_('Request Header Fields Too Large'),
			451 => T_('Unavailable For Legal Reasons'),

			500 => T_('Internal Server Error'),
			501 => T_('Not Implemented'),
			502 => T_('Bad Gateway'),
			503 => T_('Service Unavailable'),
			504 => T_('Gateway Timeout'),
			505 => T_('HTTP Version Not Supported'),
			506 => T_('Variant Also Negotiates'),
			507 => T_('Insufficient Storage'),
			510 => T_('Not Extended'),
			511 => T_('Network Authentication Required'),
		];

		if(isset($headers_list[$_code]))
		{
			return $headers_list[$_code];
		}
		// find nothing
		return null;
	}


	/**
	 * Set HTTP status header.
	 * @param int    $_code       new HTTP status code
	 */
	public static function status($_code, $_title = null)
	{
		$desc = self::desc($_code);
		if(!$desc)
		{
			return false;
		}

		$debug_backtrace = ['args' => func_get_args(), 'debug' => debug_backtrace(), 'server' => $_SERVER];
		$debug_backtrace = json_encode($debug_backtrace, JSON_UNESCAPED_UNICODE);
		\dash\db::log($debug_backtrace, null, "$_code.header");

		$status_header = "HTTP/1.1 $_code $desc";
		// set header
		@header($status_header, true, $_code);

		if(\dash\request::json_accept() || \dash\request::ajax())
		{
			if(!$_title)
			{
				$_title = self::desc($_code);
			}

			\dash\notif::error($_title, ['title'=> T_(self::desc($_code)).' '. \dash\utility\human::fitNumber($_code)]);
  			// end process code and return as json
			\dash\code::end();

			// remove below code if have no problem
			// @header('Content-Type: application/json');
			// echo \dash\notif::json();
		}
		else
		{
			$debug_backtrace = debug_backtrace(true);
			require_once(lib."engine/error_page.php");
		}

		\dash\code::exit();
	}
}
?>