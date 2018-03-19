<?php
namespace lib;

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

			self::$HEADER = \lib\safe::safe($my_header);
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
			100 => 'Continue',
			101 => 'Switching Protocols',
			102 => 'Processing',

			200 => 'OK',
			201 => 'Created',
			202 => 'Accepted',
			203 => 'Non-Authoritative Information',
			204 => 'No Content',
			205 => 'Reset Content',
			206 => 'Partial Content',
			207 => 'Multi-Status',
			226 => 'IM Used',

			300 => 'Multiple Choices',
			301 => 'Moved Permanently',
			302 => 'Found',
			303 => 'See Other',
			304 => 'Not Modified',
			305 => 'Use Proxy',
			306 => 'Reserved',
			307 => 'Temporary Redirect',
			308 => 'Permanent Redirect',

			400 => 'Bad Request',
			401 => 'Unauthorized',
			402 => 'Payment Required',
			403 => 'Forbidden',
			404 => 'Not Found',
			405 => 'Method Not Allowed',
			406 => 'Not Acceptable',
			407 => 'Proxy Authentication Required',
			408 => 'Request Timeout',
			409 => 'Conflict',
			410 => 'Gone',
			411 => 'Length Required',
			412 => 'Precondition Failed',
			413 => 'Request Entity Too Large',
			414 => 'Request-URI Too Long',
			415 => 'Unsupported Media Type',
			416 => 'Requested Range Not Satisfiable',
			417 => 'Expectation Failed',
			418 => 'I\'m a teapot',
			421 => 'Misdirected Request',
			422 => 'Unprocessable Entity',
			423 => 'Locked',
			424 => 'Failed Dependency',
			426 => 'Upgrade Required',
			428 => 'Precondition Required',
			429 => 'Too Many Requests',
			431 => 'Request Header Fields Too Large',
			451 => 'Unavailable For Legal Reasons',

			500 => 'Internal Server Error',
			501 => 'Not Implemented',
			502 => 'Bad Gateway',
			503 => 'Service Unavailable',
			504 => 'Gateway Timeout',
			505 => 'HTTP Version Not Supported',
			506 => 'Variant Also Negotiates',
			507 => 'Insufficient Storage',
			510 => 'Not Extended',
			511 => 'Network Authentication Required',
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

		$status_header = "HTTP/1.1 $_code $desc";
		// set header
		@header($status_header, true, $_code);

		if(\lib\request::json_accept() || \lib\temp::get('api'))
		{
			@header('Content-Type: application/json');

			\lib\notif::title($desc);
			\lib\notif::error($_title, $_code, "HTTP");
			echo \lib\notif::json();
		}
		else
		{
			$debug_backtrace = debug_backtrace(true);
			require_once(lib."engine/error_page.php");
		}

		\lib\code::exit();
	}
}
?>