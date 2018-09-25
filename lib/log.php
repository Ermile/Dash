<?php
namespace dash;


class log
{
	use \dash\log\load;
	use \dash\log\user;
	use \dash\log\send;

	// check to not duplicate load notification file
	private static $load               = false;
	// check to not duplicate load user data
	private static $user_loaded        = false;
	// list of project notifissin list
	private static $project_notif_list = [];
	// list of dash notification list
	private static $core_notif_list    = [];

	private static $notif_detail       = [];

	private static $all_user_detail    = [];

	// save log in database
	public static function db($_caller, $_args = [])
	{
		return \dash\db\logs::set($_caller, \dash\user::id(), $_args);
	}


	public static function set($_caller, $_args = [])
	{
		self::clean();

		$all_list = self::lists();

		if(!isset($all_list[$_caller]))
		{
			return self::db(...func_get_args());
		}

		$caller_detail = $all_list[$_caller];

		if(isset($caller_detail['notification']) && $caller_detail['notification'])
		{
			return self::send(...func_get_args());
		}

		return self::db(...func_get_args());
	}


	private static function detail($_key = null, $_sub_key = null)
	{
		$detail = self::$notif_detail;
		if(!$detail)
		{
			return null;
		}

		if($_key)
		{
			if($_sub_key)
			{
				if(array_key_exists($_key, $detail))
				{
					if(is_array($detail[$_key]) && array_key_exists($_sub_key, $detail[$_key]))
					{
						return $detail[$_key][$_sub_key];
					}
					else
					{
						return null;
					}
				}
				else
				{
					return null;
				}
			}
			else
			{
				if(array_key_exists($_key, $detail))
				{
					return $detail[$_key];
				}
				else
				{
					return null;
				}
			}
		}
		else
		{
			return $detail;
		}
	}


	private static function detail_set($_key = null, $_sub_key = null, $_value = null)
	{
		$detail = self::$notif_detail;
		if(!$detail)
		{
			return null;
		}

		if($_key)
		{
			if($_sub_key)
			{
				if(array_key_exists($_key, $detail))
				{
					if(is_array($detail[$_key]) && array_key_exists($_sub_key, $detail[$_key]))
					{
						$detail[$_key][$_sub_key] = $_value;
					}
				}
			}
			else
			{
				if(array_key_exists($_key, $detail))
				{
					$detail[$_key] = $_value;
				}
			}
		}

		self::$notif_detail = $detail;
	}



}
?>