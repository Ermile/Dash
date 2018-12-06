<?php
namespace content_account\billing;

class controller
{
	public static function routing()
	{
		if(!\dash\option::config('billing_page'))
		{
			\dash\header::status(403, T_("This page is locked!"));
		}
	}
}