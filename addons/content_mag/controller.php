<?php
namespace content_mag;

class controller
{
	/**
	 * rout
	 */
	public static function routing()
	{
		if(!\dash\option::config('mag'))
		{
			\dash\header::status(404);
		}
	}
}
?>