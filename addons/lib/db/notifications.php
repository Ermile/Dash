<?php
namespace lib\db;

/** notifications managing **/
class notifications
{
	/**
	 * insert new notification
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function insert()
	{
		return \lib\db\config::public_insert('notifications', ...func_get_args());
	}


	/**
	 * update the notification
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function update()
	{
		return \lib\db\config::public_update('notifications', ...func_get_args());
	}


	/**
	 * get the notification
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function get()
	{
		return \lib\db\config::public_get('notifications', ...func_get_args());
	}


	/**
	 * Searches for the first match.
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function search()
	{
		return \lib\db\config::public_search('notifications', ...func_get_args());
	}


	/**
	 * Gets not sended notifications
	 *
	 * @return     <type>  Not sended.
	 */
	public static function get_not_sended()
	{
		$query = "SELECT * FROM notifications WHERE notifications.senddate IS NULL";
		return \lib\db::get($query);
	}


	/**
	 * set new notify
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public static function set($_args)
	{
		if(!is_array($_args))
		{
			$_args = [];
		}

		$default_args =
		[
			'to'         => null,
			'content'    => null,
			'title'      => null,
			'cat'        => null,
			'from'       => null,
			'url'        => null,
			'read'       => null,
			'status'     => 'enable',
			'expiredate' => null,
			'desc'       => null,
			'meta'       => null,
			'telegram'   => false,
			'sms'        => false,
			'email'      => false,
		];

		$_args = array_merge($default_args, $_args);

		if(!$_args['to']) return false;
		if(!isset($_args['content'])) return false;
		if(!$_args['cat']) return false;

		$cat_detail         = [];
		$cat_id             = null;

		$all_cat_list       = \lib\option::config('notification', 'cat');
		$all_cat_list_title = array_column($all_cat_list, 'title');
		$all_cat_list_title = array_combine(array_keys($all_cat_list), $all_cat_list_title);

		if(($key = array_search($_args['cat'], $all_cat_list_title)) !== false)
		{
			$cat_detail = $all_cat_list[$key];
			$cat_id = $key;
			if(isset($cat_detail['send_by']) && is_array($cat_detail['send_by']))
			{
				foreach ($cat_detail['send_by'] as $value)
				{
					$_args[$value] = true;
				}
			}
		}

		$insert =
		[
			'user_id'       => $_args['to'],
			'user_idsender' => $_args['from'],
			'title'         => $_args['title'],
			'content'       => $_args['content'],
			'category'      => $cat_id,
			'telegram'      => $_args['telegram'] ? 1 : null,
			'sms'           => $_args['sms'] ? 1 : null,
			'email'         => $_args['email'] ? 1 : null,
			'url'           => $_args['url'],
			'read'          => $_args['read'],
			'status'        => $_args['status'],
			'expiredate'    => $_args['expiredate'],
			'desc'          => $_args['desc'],
			'meta'          => $_args['meta'],
		];

		return self::insert($insert);
	}

}
?>