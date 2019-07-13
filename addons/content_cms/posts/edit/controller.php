<?php
namespace content_cms\posts\edit;

class controller
{

	public static function routing()
	{

		$id = \dash\request::get('id');
		$detail = \dash\app\posts::load_post($id);
		if(!$detail)
		{
			\dash\header::status(403, T_("Invalid id"));
		}

		\dash\data::dataRow($detail);

	}
}
?>