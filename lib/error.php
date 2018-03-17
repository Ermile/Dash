<?php
namespace lib;

/**
 * Class for error.
 * make error page
 */
class error
{
	public static function config($_code)
	{
		$error                  = [];
		$error['bad']           = ['code' => 400, 'title' => 'BAD REQUEST'];
		$error['login']         = ['code' => 401, 'title' => 'UNAUTHORIZED'];
		$error['access']        = ['code' => 403, 'title' => 'FORBIDDEN'];
		$error['page']          = ['code' => 404, 'title' => 'NOT FOUND'];
		$error['core']          = ['code' => 404, 'title' => 'NOT FOUND'];
		$error['method']        = ['code' => 405, 'title' => 'METHOD NOT ALLOWED'];
		$error['notacceptable'] = ['code' => 406, 'title' => 'NOT ACCEPTABLE'];
		$error['timeout']       = ['code' => 408, 'title' => 'REQUEST TIME OUT'];
		$error['gone']          = ['code' => 410, 'title' => 'GONE'];
		$error['length']        = ['code' => 411, 'title' => 'LENGTH REQUIRED'];
		$error['recondition']   = ['code' => 412, 'title' => 'PRECONDITION FAILED'];
		$error['large']         = ['code' => 413, 'title' => 'REQUEST ENTITY TOO LARGE'];
		$error['uritoolarg']    = ['code' => 414, 'title' => 'REQUEST URI TOO LARGE'];
		$error['type']          = ['code' => 415, 'title' => 'UNSUPPORTED MEDIA TYPE'];
		$error['internal']      = ['code' => 500, 'title' => 'INTERNAL SERVER ERROR'];
		$error['unsupport']     = ['code' => 501, 'title' => 'NOT IMPLEMENTED'];
		$error['gateway']       = ['code' => 502, 'title' => 'BAD GATEWAY'];
		$error['service']       = ['code' => 503, 'title' => 'SERVICE UNAVAILABLE'];
		$error['variant']       = ['code' => 506, 'title' => 'VARIANT ALSO VARIES'];

		if(isset($error[$_code]))
		{
			return $error[$_code];
		}
		return null;
	}


	public static function __callStatic($_fn, $_args = null)
	{
		if($error = self::config($_fn))
		{
			$subtitle = null;
			if(isset($_args[0]) && is_string($_args[0]))
			{
				$subtitle = $_args[0];
			}
			self::make($error['code'], $error['title'], $subtitle);
		}
		else
		{
			\lib\code::exit("function not exist");
		}
	}


	public static function make($_code, $_title, $_subtitle)
	{
		$HTTP_ERROR = $_title;
		$subtitle   = $_subtitle;
		$obj        = debug_backtrace(true);

		if(\lib\request::json_accept() || \lib\temp::get('api'))
		{
			header('Content-Type: application/json');
			header("HTTP/1.1 $_code ".$HTTP_ERROR);
			\lib\notif::title($HTTP_ERROR);
			\lib\notif::error($_title, $_code, "HTTP");
			echo \lib\notif::compile(true);
		}
		else
		{
			header("HTTP/1.1 $_code ".$HTTP_ERROR);
			require_once(lib."engine/error_page.php");
		}
		\lib\code::exit();
	}


	// error handler function
	public static function myErrorHandler($errno = null, $errstr = null, $errfile = null, $errline = null)
	{
		// This error code is not included in error_reporting
		if (!(error_reporting() & $errno))
		{
			return;
		}

		echo "<pre>";
		switch ($errno)
		{
			case E_USER_ERROR:
				echo "<b>My ERROR</b> [$errno] $errstr<br />\n";
				echo "  Fatal error on line $errline in file $errfile";
				echo ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
				echo "Aborting...<br />\n";
				\lib\code::exit();
				break;

			case E_USER_WARNING:
				echo "<b>My WARNING</b> [$errno] $errstr<br />\n";
				break;

			case E_USER_NOTICE:
				echo "<b>My NOTICE</b> [$errno] $errstr<br />\n";
				break;

			default:
				echo "<b>Unknown error type</b>: [$errno] $errstr<br />\n";
				break;
		}
		echo "</pre>";

		/* Don't execute PHP internal error handler */
		return true;
	}
}
?>