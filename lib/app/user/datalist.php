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
			'sort'  => null,
			'order' => null,
		];

		$_args = array_merge($default_args, $_args);

		if($_args['sort'] && !in_array($_args['sort'], self::$sort_field))
		{
			$_args['sort'] = null;
		}

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