<?php
namespace dash\db;

/** telegrams managing **/
class telegrams
{
	public static function insert($_args)
	{
		$_args = \dash\safe::safe($_args, 'raw-nottrim');
		return \dash\db\config::public_insert('telegrams', $_args, \dash\db::get_db_log_name());
	}


	public static function multi_insert($_args)
	{
		$_args = \dash\safe::safe($_args, 'raw-nottrim');
		return \dash\db\config::public_multi_insert('telegrams', $_args, \dash\db::get_db_log_name());
	}


	public static function update($_args, $_id)
	{
		$_args = \dash\safe::safe($_args, 'raw-nottrim');
		return \dash\db\config::public_update('telegrams', $_args, $_id, \dash\db::get_db_log_name());
	}


	public static function get($_where, $_option = [])
	{
		return \dash\db\config::public_get('telegrams', $_where, ['db_name' => \dash\db::get_db_log_name()]);
	}


	public static function search($_string = null, $_option = [])
	{
		$default =
		[
			'db_name' => \dash\db::get_db_log_name(),
		];

		if(isset($_option['join_user']))
		{
			$db_name = db_name;

			$default['public_show_field'] =
			"
				telegrams.*,
				$db_name.users.displayname,
				$db_name.users.mobile,
				$db_name.users.avatar
			";

			$default['master_join'] =
			"
				LEFT JOIN $db_name.users ON $db_name.users.id = telegrams.user_id
			";

		}
		elseif(isset($_option['group_by_chatid']))
		{

			$default['public_show_field'] =
			"
				COUNT(*) AS `count`,
				telegrams.chatid AS `chatid`,
				MIN(telegrams.user_id) AS `user_id`
			";
			$default['group_by'] =
			"
				GROUP BY telegrams.chatid
			";
			$default['order_raw'] = " `count` DESC";

		}

		unset($_option['group_by_chatid']);
		unset($_option['join_user']);

		if(!is_array($_option))
		{
			$_option = [];
		}

		$_option = array_merge($default, $_option);

		$result = \dash\db\config::public_search('telegrams', $_string, $_option);
		return $result;
	}

}
?>
