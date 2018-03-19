<?php
namespace lib\engine;


class dev
{
	public static function debug()
	{
		if(\lib\option::config('debug'))
		{
			return true;
		}
		return false;
	}


	public static function set_php_ini()
	{
		// change header and remove php from it
		@header("X-Made-In: Ermile!");
		@header("X-Powered-By: Dash!");

		if (\lib\option::config('debug'))
		{
			ini_set('display_startup_errors', 'On');
			ini_set('error_reporting'       , 'E_ALL | E_STRICT');
			ini_set('track_errors'          , 'On');
			ini_set('display_errors'        , 1);
			error_reporting(E_ALL);
		}
		else
		{
			error_reporting(0);
			ini_set('display_errors', 0);
		}

		//Setting for the PHP Error Handler
		set_error_handler( "\\lib\\engine\\error::handle_error" );

		//Setting for the PHP Exceptions Error Handler
		set_exception_handler( "\\lib\\engine\\error::handle_exception" );

		//Setting for the PHP Fatal Error
		register_shutdown_function( "\\lib\\engine\\error::handle_fatal" );
	}

}
?>
