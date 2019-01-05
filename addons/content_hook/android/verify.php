<?php
namespace content_hook\android;


class verify
{
	public static function token()
	{
		$token_is_ok = true;
		if(!$token_is_ok)
		{
			\dash\header::status(403);
		}
	}

	public static function called_url()
	{

	}
}
?>