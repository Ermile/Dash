<?php
namespace dash\app\user;

trait datalist
{
	public static $sort_field =
	[
		'id' ,
		'username' ,
		'displayname' ,
		'gender' ,
		'title' ,
		'password' ,
		'mobile' ,
		'email' ,
		'status' ,
		'avatar' ,
		'parent' ,
		'permission' ,
		'type' ,
		'datecreated' ,
		'datemodified' ,
		'pin' ,
		'ref' ,
		'twostep' ,
		'birthday' ,
		'unit_id' ,
		'language' ,
		'meta' ,
		'birthday',
		'website',
		'facebook',
		'twitter',
		'instagram',
		'linkedin',
		'gmail',
		'sidebar',
		'firstname',
		'lastname',
		'bio',
	];

	/**
	 * Gets the user.
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  The user.
	 */
	public static function list($_string = null, $_args = [])
	{
		if(!\dash\user::id())
		{
			return false;
		}

		$default_args =
		[
			'sort'            => null,
			'order'           => null,
			'check_duplicate' => null,
		];

		$_args = array_merge($default_args, $_args);

		if($_args['sort'] && !in_array($_args['sort'], self::$sort_field))
		{
			$_args['sort'] = null;
		}

		if(!\dash\permission::supervisor())
		{
			$_args['3.14'] = ["= 3.14", " AND ( users.permission != 'supervisor'  OR `permission` IS NULL OR `permission` = '' ) "];
		}

		if($_args['check_duplicate'])
		{
			$_args['search_field']                      = '';
			$_args['public_show_field']                 = " COUNT(*) AS `count`, users.". $_args['check_duplicate'];
			$_args['users.'. $_args['check_duplicate']] = [ "IS NOT", " NULL "];
			$_args['group_by']                          = " GROUP BY users.". $_args['check_duplicate'];
			$_args['order']                             = null;
			$_args['sql_having']                        = " HAVING COUNT(*) >= 2";
			$_args['order_raw']                         = "COUNT(*) DESC";
			$_args['sort']                              = null;

		}

		if(isset($_args['join_user_telegram']) && $_args['join_user_telegram'])
		{
			$_args['public_show_field'] = "users.*, user_telegram.chatid";
			$_args['master_join'] = "INNER JOIN user_telegram ON user_telegram.user_id = users.id";
		}

		if(isset($_args['join_user_android']) && $_args['join_user_android'])
		{
			$_args['public_show_field'] = "users.*, user_android.id as `android_uniquecode`";
			$_args['master_join'] = "INNER JOIN user_android ON user_android.user_id = users.id";
		}


		unset($_args['check_duplicate']);
		unset($_args['join_user_telegram']);
		unset($_args['join_user_android']);

		$meta            = $_args;
		$result          = \dash\db\users::search($_string, $meta);
		$temp            = [];
		foreach ($result as $key => $value)
		{
			$check = self::ready($value);
			if($check)
			{
				$temp[] = $check;
			}
		}

		return $temp;
	}
}
?>