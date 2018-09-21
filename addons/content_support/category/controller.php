<?php
namespace content_support\category;

class controller
{

	public static function routing()
	{
		$child = \dash\url::child();
		if(!$child)
		{
			\dash\redirect::to(\dash\url::here());
		}

		$check = \dash\db\terms::get(['slug' => $child, 'type' => 'help', 'limit' => 1]);
		if(!$check)
		{
			\dash\header::status(404, T_("Invalid category"));
		}
		\dash\data::categoryDetail($check);

		\dash\open::get();
	}
}
?>