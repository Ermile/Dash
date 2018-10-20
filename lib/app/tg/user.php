<?php
namespace dash\app\tg;


class user
{
	public static function get($_chat_id)
	{
		$get = \dash\db\users::get(['chatid' => $_chat_id, 'limit' => 1]);
		return $get;
	}


	public static function init($_user_code)
	{
		// $user_id = \dash\coding::decode($_user_code);
		$user_id = $_user_code;

		if($user_id)
		{
			return \dash\user::init($user_id);
		}
	}


	public static function add($_args, $_option = [])
	{
		return \dash\app\user::add($_args, ['force_add' => true]);
	}


	public static function lang($_lang = null)
	{
		if(!$_lang)
		{
			return \dash\user::detail('language');
		}
		else
		{

			if(self::id() && mb_strlen($_lang) === 2)
			{
				$update = \dash\db\users::update(['language' => $_lang], self::id());

				if($update)
				{
					\dash\app\tg\account::relogin();
					return true;
				}
				else
				{
					return false;
				}
			}
		}
		return false;
	}


	public static function status($_status = null)
	{
		if(!$_status)
		{
			return \dash\user::detail('tgstatus');
		}
		else
		{
			if(self::id())
			{
				$update = \dash\db\users::update(['tgstatus' => $_status], self::id());
				if($update)
				{
					\dash\app\tg\account::relogin();
					return true;
				}
				else
				{
					return false;
				}
			}
		}
		return false;
	}


	public static function id()
	{
		return \dash\user::id();
		// return \dash\coding::decode(\dash\user::id());
	}


}
?>