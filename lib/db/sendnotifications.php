<?php
namespace dash\db;

/** sendnotifications managing **/
class sendnotifications
{
	/**
	 * insert new notification
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function insert()
	{
		return \dash\db\config::public_insert('sendnotifications', ...func_get_args());
	}


	/**
	 * make multi insert
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function multi_insert()
	{
		return \dash\db\config::public_multi_insert('sendnotifications', ...func_get_args());
	}


	/**
	 * update the notification
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function update()
	{
		return \dash\db\config::public_update('sendnotifications', ...func_get_args());
	}


	/**
	 * get the notification
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function get()
	{
		return \dash\db\config::public_get('sendnotifications', ...func_get_args());
	}


	/**
	 * Searches for the first match.
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function search()
	{
		$result = \dash\db\config::public_search('sendnotifications', ...func_get_args());
		return $result;
	}


	public static function not_sended()
	{
		$query = "SELECT * FROM sendnotifications WHERE sendnotifications.status = 'awaiting' ";
		return \dash\db::get($query);
	}


	public static function set_status($_status, $_ids)
	{
		$_ids = array_filter($_ids);
		$_ids = array_unique($_ids);

		if($_ids)
		{
			$_ids = implode(',', $_ids);
			$query = "UPDATE sendnotifications SET sendnotifications.status = '$_status' WHERE sendnotifications.id IN ($_ids) ";
			return \dash\db::query($query);
		}
	}



}
?>
