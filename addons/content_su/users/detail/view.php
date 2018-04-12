<?php
namespace content_su\users\detail;

class view
{
	public static function config()
	{
		$id     = \dash\request::get('id');
		$id     = \dash\coding::decode($id);
		$result = [];

		if($id && is_numeric($id))
		{
			$result = \dash\db\users::get_by_id($id);
		}

		\dash\data::userRecord($result);
	}
}
?>