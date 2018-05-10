<?php
namespace content_cp\posts\edit;

class controller
{

	public static function ready()
	{
		$id = \dash\request::get('id');
		if(!$id || !\dash\coding::is($id))
		{
			\dash\header::status(404, T_("Invalid id"));
		}
	}
}
?>