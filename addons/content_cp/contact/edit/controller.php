<?php
namespace content_cp\contact\edit;

class controller
{

	public static function routing()
	{
		\dash\permission::access('cpContactChangeStatus');

		\dash\redirect::to(\dash\url::kingdom(). '/support/ticket?access=all&status=all&q='. T_("Contact Us"));
		return;

		// $id = \dash\request::get('id');

		// if(!$id || !\dash\coding::is($id))
		// {
		// 	\dash\header::status(404, T_("Invalid id"));
		// }
	}
}
?>