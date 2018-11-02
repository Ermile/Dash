<?php
namespace dash\engine;

class error
{

	public static function handle_exception($e)
	{
		@header("HTTP/1.1 506", true, 506);
		$msg = $e->getFile(). " : " . $e->getLine(). "\n";
		$msg .= \get_class($e). "(". $e->getMessage(). ")";
		self::log($msg, 'exception.log', 'php');
		self::show_error($msg);
	}


	public static function handle_fatal()
	{
		$error = error_get_last();
		if ($error["type"] == E_ERROR)
		{
			self::handle_error($error["type"], $error["message"], $error["file"], $error["line"]);
		}
	}


	/**
	 * error handler function
	 * @param  [type] $_level   [description]
	 * @param  [type] $_msg  [description]
	 * @param  [type] $_file [description]
	 * @param  [type] $_line [description]
	 * @return [type]          [description]
	 */
	public static function handle_error($_level = null, $_msg = null, $_file = null, $_line = null)
	{
		// This error code is not included in error_reporting
		if (!(error_reporting() & $_level))
		{
			return;
		}

		$msg = "$_file : $_line\n";
		$msg .= "[$_level] ($_msg)";

		// echo "</pre>";
		if(is_numeric($_level))
		{
			$type = $_level;
		}
		else
		{
			$type = 'unknown';
		}
		self::log($msg, $type.'.log', 'php');
		self::show_error($msg);

		/* Don't execute PHP internal error handler */
		return true;
	}

	public static function show_error($_msg)
	{
		if(\dash\engine\dev::debug())
		{
			echo '<pre>';
			echo $_msg;
			echo '</pre>';
		}
	}


	/**
	 * save text into file to check later!
	 * @param  [type] $_text [description]
	 * @return [type]        [description]
	 */
	public static function log($_text, $_name = 'error.log', $_group = 'general')
	{
		$date_now        = new \DateTime("now", new \DateTimeZone('Asia/Tehran') );
		$debug_backtrace = array_column(debug_backtrace(), 'file');
		$directory_addr  = root.'includes/log/'. $_group. '/';
		$file_addr       = $directory_addr. $_name;

		// start saving
		\dash\file::makeDir($directory_addr, null, true);

		// set evenet datetime and file address
		$my_text  = "#". str_repeat("-", 10);
		$my_text .= $date_now->format("Y-m-d H:i:s");
		$my_text .= str_repeat("-", 50). ' ';
		$my_text .= \dash\user::id(). ' - ';
		if(is_callable("\dash\url::pwd"))
		{
			$my_text .= urldecode(\dash\url::pwd());
		}
		$my_text .= "\n";

		// add final text
		$my_text .= trim($_text);
		$my_text .= "\r\n";

		@file_put_contents($file_addr, $my_text. PHP_EOL, FILE_APPEND);
	}
}
?>