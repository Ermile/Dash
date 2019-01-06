<?php
namespace dash\app\tg;


class user
{
	public static function hard_delete($_chat_id)
	{
		$user_detail = self::get($_chat_id);
		$result = null;
		if(isset($user_detail['id']))
		{
			$result = \dash\db\users::hard_delete($user_detail['id']);
			if(!$result)
			{
				$set =
				[
					'mobile'     => null,
					'username'   => null,
					'chatid'     => null,
					'password'   => null,
					'email'      => null,
					'tgstatus'   => null,
					'tgusername' => null,
					'title'      => 'deleted by telegram',
				];

				$where =
				[
					'chatid' => $_chat_id,
				];

				$result = \dash\db\users::update_where($set, $where);
			}
		}

		return $result;
	}

	public static function get($_chat_id)
	{
		if(!$_chat_id)
		{
			return null;
		}
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
		$_args['force_add'] = true;
		return \dash\app\user::add($_args, ['force_add' => true, 'encode' => false]);
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