<?php
namespace content_su\notifications;

class view
{
	public static function config()
	{
		$list  = self::notifications_list();

		\dash\data::notificationsList($list);

	}

	public static  function notifications_list()
	{
		$meta   = [];
		$meta['admin'] = true;
		$search = null;
		if(\dash\request::get('search'))
		{
			$search = \dash\request::get('search');
		}

		$result = \dash\db\notifications::search($search, $meta);

		return $result;
	}

}
?>