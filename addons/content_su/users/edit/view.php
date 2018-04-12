<?php
namespace content_su\users\edit;

class view
{
	public static function config()
	{
		\dash\data::editMode(true);
		\dash\data::userDetail(\dash\db\users::get_by_id(\dash\coding::decode(\dash\request::get('id'))));
	}
}
?>