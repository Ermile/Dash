<?php
namespace content_cp\backup;

class controller
{
	public static function routing()
	{
		if(\dash\option::config('full_backup') || \dash\permission::supervisor())
		{
			// no problem
		}
		else
		{
			\dash\header::status(403);
		}
	}
}
?>