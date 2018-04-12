<?php
namespace content_su\logs;

class view
{
	public static function config()
	{
		\dash\data::page_title(T_("Logs list"));
		\dash\data::page_desc(T_("All event in this system"));
		$list                  = self::logs_list();
		\dash\data::logsList($list);
	}


	public static function logs_list()
	{
		$meta          = [];
		$meta['admin'] = true;
		$search        = null;
		if(\dash\request::get('search'))
		{
			$search = \dash\request::get('search');
		}

		$result = \dash\db\logs::search($search, $meta);

		return $result;
	}
}
?>