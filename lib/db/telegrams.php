<?php
namespace dash\db;

/** telegrams managing **/
class telegrams
{
	public static function insert($_args)
	{
		return \dash\db\config::public_insert('telegrams', $_args, \dash\db::get_db_log_name());
	}


	public static function multi_insert($_args)
	{
		return \dash\db\config::public_multi_insert('telegrams', $_args, \dash\db::get_db_log_name());
	}


	public static function update($_args, $_id)
	{
		return \dash\db\config::public_update('telegrams', $_args, $_id, \dash\db::get_db_log_name());
	}


	public static function get($_where, $_option = [])
	{
		return \dash\db\config::public_get('telegrams', $_where, ['db_name' => \dash\db::get_db_log_name()]);
	}


	public static function search($_string = null, $_option = [])
	{
		if(isset($_option['join_user']))
		{

			$db_name = db_name;

			$default =
			[

				"public_show_field" =>
				"
					telegrams.*,

					$db_name.users.displayname,
					$db_name.users.mobile,
					$db_name.users.avatar

				",
				"master_join"       =>
				"
					LEFT JOIN $db_name.users ON $db_name.users.id = telegrams.user_id
				",
				'db_name' => \dash\db::get_db_log_name(),
			];


		}
		else
		{
			$default =
			[
				'db_name' => \dash\db::get_db_log_name(),
			];

		}

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
