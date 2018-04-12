<?php
namespace content_su\users;

class view
{
	public static function config()
	{
		$list = self::users_list();
		\dash\data::usersList($list);
	}


	public static function users_list()
	{
		$meta          = [];
		$meta['admin'] = true;
		$search        = null;

		if(\dash\request::get('search'))
		{
			$search = \dash\request::get('search');
		}

		$result = \dash\db\users::search($search, $meta);
		return $result;
	}
}
?>