<?php
namespace content_su\logitems;

class view
{

	public static function config()
	{
		$list = self::logitems_list();
		\dash\data::logitemsList($list);
	}


	public static function logitems_list()
	{
		$meta          = [];
		$meta['admin'] = true;
		$search        = null;

		if(\dash\request::get('search'))
		{
			$search = \dash\request::get('search');
		}

		$result = \dash\db\logitems::search($search, $meta);

		return $result;
	}

}
?>