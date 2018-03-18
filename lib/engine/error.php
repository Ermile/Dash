<?php
namespace lib\engine;

class error
{
	/**
	 * error handler function
	 * @param  [type] $errno   [description]
	 * @param  [type] $errstr  [description]
	 * @param  [type] $errfile [description]
	 * @param  [type] $errline [description]
	 * @return [type]          [description]
	 */
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