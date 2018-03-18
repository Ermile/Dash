<?php
namespace addons\content_enter\main\tools;


trait error
{

	/**
	 * set redirect url
	 *
	 * @param      <type>  $_url   The url
	 */
	public static function error_page($_module)
	{
		switch ($_module)
		{
			default:
				\lib\header::status(404, $_module);
				break;
		}
	}


	/**
	 * error method
	 * the user send data to us bu other method GET, POST
	 * @param      <type>  $_module  The module
	 */
	public static function error_method($_module)
	{
		switch ($_module)
		{
			default:
				\lib\header::status(404, $_module);
				break;
		}
	}
}