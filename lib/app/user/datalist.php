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
		'chatid' ,
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

		if(!\dash\url::isLocal() && !\dash\permission::supervisor())
		{
			$_args['permission'] = ["!=", " 'supervisor' OR `permission` IS NULL OR `permission` = '' "];
		}

		if($_args['check_duplicate'])
		{
			$_args['search_field']      = '';
			$_args['public_show_field'] = " COUNT(*) AS `count`, users.". $_args['check_duplicate'];
			$_args['group_by']          = " GROUP BY users.". $_args['check_duplicate'];
			$_args['order']             = null;
			$_args['sql_having']        = " HAVING COUNT(*) >= 2";
			$_args['order_raw']         = "COUNT(*) DESC";
			$_args['sort']              = null;

		}

		unset($_args['check_duplicate']);


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