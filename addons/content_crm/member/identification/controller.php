<?php
namespace content_crm\member\identification;


class controller
{
	public static function routing()
	{
		\dash\permission::access('aMemberView');
		if(!\dash\request::get('id'))
		{
			\dash\header::status(404, T_("Id not found"));
		}
	}
}
?>