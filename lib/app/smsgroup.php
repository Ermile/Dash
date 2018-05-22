<?php
namespace dash\app;


class smsgroup
{

	public static function get($_id)
	{
		$list = self::list();
		if(!isset($list[$_id]))
		{
			return false;
		}

		if(is_callable(['\lib\smsgroup', 'get']))
		{
			return \lib\smsgroup::get($_id);
		}

		$result = [];

		switch ($_id)
		{
			case 'alluser':
				$result = \dash\db\users::all_user_mobile();
				break;

			case 'activeuser':
				$result = \dash\db\users::all_user_mobile(['status' => 'active']);
				break;
		}

		return $result;
	}


	public static function list()
	{
		$list = [];
		if(is_callable(['\lib\smsgroup', 'list']))
		{
			return \lib\smsgroup::list();
		}

		$list['alluser']    = T_("All users");
		$list['activeuser'] = T_("Active user");
		return $list;
	}
}
?>
