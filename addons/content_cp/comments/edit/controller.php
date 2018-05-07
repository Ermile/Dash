<?php
namespace content_cp\comments\edit;

class controller
{

	public static function routing()
	{
		\dash\permission::access('cpCommentsEdit');

		$id = \dash\request::get('id');

		if(!$id || !\dash\coding::is($id))
		{
			\dash\header::status(404, T_("Invalid id"));
		}
	}
}
?>