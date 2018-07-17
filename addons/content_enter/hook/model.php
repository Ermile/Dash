<?php
namespace content_enter\hook;


class model
{
	public static $user_id = null;


	public static function delete()
	{
		if(self::check_api_key())
		{
			$telegram_id = \dash\utility::request("telegramid");


			if(!$telegram_id)
			{
				\dash\notif::error(T_("Telegram id not found"), 'telegram_id', 'post');
				return false;
			}

			if(!is_numeric($telegram_id))
			{
				\dash\notif::error(T_("Invalid telegram id"), 'telegram_id', 'post');
				return false;
			}

			$where =
			[
				'chatid' => $telegram_id,
				'limit'  => 1
			];

			$exist_chart_id = \dash\db\config::public_get('users', $where);

			$log_meta =
			[
				'meta' =>
					[
						'request'        => \dash\utility::request(),
						'record_chat_id' => $exist_chart_id,
					],
			];
			if(isset($exist_chart_id['id']))
			{
				$remove_all_this_chat_id = "UPDATE users SET chatid = NULL WHERE chatid = '$telegram_id' ";
				\dash\db::query($remove_all_this_chat_id);
				\dash\db\logs::set('enter:hook:remove:all:chat:id:by:delete:request', $exist_chart_id['id'], $log_meta);
			}
		}

		if(\dash\engine\process::status())
		{
			// \dash\notif::title(T_("Operation complete"));
		}
		else
		{
			// \dash\notif::title(T_("Operation faild"));
		}
	}

	/**
	 * get user data
	 */
	public static function post()
	{
		if(self::check_api_key())
		{
			self::config_user_data();
		}

		if(\dash\engine\process::status())
		{
			// \dash\notif::title(T_("Operation complete"));
		}
		else
		{
			// \dash\notif::title(T_("Operation faild"));
		}
	}


	/**
	 * check api key and set the user id
	 */
	public static function check_api_key()
	{
		$authorization = \dash\header::get("authorization");

		if(!$authorization)
		{
			$authorization = \dash\header::get("Authorization");
		}

		if(!$authorization)
		{
			\dash\notif::error(T_('Authorization not found'), 'authorization', 'access');
			return false;
		}

		if($authorization === \dash\option::config('enter', 'telegram_hook'))
		{
			return true;
		}
		else
		{
			\dash\notif::error(T_('Invalid Authorization'), 'authorization', 'access');
			return false;
		}

	}


	/**
	 * the api telegram token
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public static function config_user_data()
	{
		\dash\log::db('telegramHook');

		$telegram_id = \dash\utility::request("tg_id");
		$first_name  = \dash\utility::request('tg_first_name');
		$last_name   = \dash\utility::request('tg_last_name');
		$username    = \dash\utility::request('tg_username');
		$started     = \dash\utility::request('tg_start');
		$ref         = \dash\utility::request('tg_ref');
		$mobile      = \dash\utility::request('tg_mobile');
		$mobile      = \dash\utility\filter::mobile($mobile);

		if(!$mobile)
		{
			\dash\notif::error(T_("Mobile is not set"), 'tg_mobile', 'post');
			return false;
		}

		if(!$telegram_id)
		{
			\dash\notif::error(T_("Telegram id not found"), 'telegram_id', 'post');
			return false;
		}

		if(!is_numeric($telegram_id))
		{
			\dash\notif::error(T_("Invalid telegram id"), 'telegram_id', 'post');
			return false;
		}

		$where =
		[
			'chatid' => $telegram_id,
			'limit'        => 1
		];

		$exist_chart_id = \dash\db\config::public_get('users', $where);

		$exist_mobile = \dash\db\users::get_by_mobile($mobile);


		$log_meta =
		[
			'meta' =>
				[
					'request'        => \dash\utility::request(),
					'record_mobile'  => $exist_mobile,
					'record_chat_id' => $exist_chart_id,
				],
		];

		if(!$exist_chart_id && !$exist_mobile)
		{
			// calc full_name of user
			$fullName = trim($first_name. ' '. $last_name);
			$fullName = \dash\safe::safe($fullName, 'sqlinjection');

			if(mb_strlen($fullName) > 50)
			{
				$fullName = null;
			}

			$insert_user                = [];
			$insert_user['mobile']      = $mobile;
			$insert_user['displayname'] = $fullName;
			$insert_user['chatid']      = $telegram_id;
			$insert_user['datecreated'] = date("Y-m-d H:i:s");
			\dash\db\users::insert($insert_user);
			self::$user_id = \dash\db::insert_id();
			\dash\db\logs::set('enter:hook:signup:new', $exist_mobile['id'], $log_meta);

		}
		elseif($exist_chart_id && $exist_mobile)
		{
			if(isset($exist_chart_id['id']) && isset($exist_mobile['id']))
			{
				if(intval($exist_chart_id['id']) === intval($exist_mobile['id']))
				{
					self::$user_id = (int) $exist_mobile['id'];
				}
				else
				{
					$remove_all_this_chat_id = "UPDATE users SET chatid = NULL WHERE chatid = '$telegram_id' ";
					\dash\db::query($remove_all_this_chat_id);
					\dash\db\logs::set('enter:hook:remove:all:chat:id', $exist_mobile['id'], $log_meta);
					\dash\db\users::update(['chatid' => $telegram_id], $exist_mobile['id']);
					self::$user_id = (int) $exist_mobile['id'];
				}
			}
			else
			{
				\dash\notif::error(T_("System error 1"));
				return false;
			}
		}
		elseif($exist_chart_id && !$exist_mobile)
		{
			if(isset($exist_chart_id['id']))
			{
				if($mobile)
				{
					$remove_all_this_chat_id = "UPDATE users SET chatid = NULL WHERE chatid = '$telegram_id' ";

					\dash\db::query($remove_all_this_chat_id);

					\dash\db\logs::set('enter:hook:remove:all:chat:id', $exist_chart_id['id'], $log_meta);

					\dash\db\users::update(['mobile' => $mobile, 'chatid' => $telegram_id], $exist_chart_id['id']);

					\dash\db\logs::set('enter:hook:change:mobile', $exist_chart_id['id'], $log_meta);
				}
				self::$user_id = (int) $exist_chart_id['id'];
			}
			else
			{
				\dash\notif::error(T_("System error 2"));
				return false;
			}
		}
		elseif(!$exist_chart_id && $exist_mobile)
		{
			if(isset($exist_mobile['id']))
			{
				if($telegram_id)
				{
					\dash\db\users::update(['chatid' => $telegram_id], $exist_mobile['id']);
					\dash\db\logs::set('enter:hook:change:chat_id', $exist_mobile['id'], $log_meta);
				}
				self::$user_id = (int) $exist_mobile['id'];
			}
			else
			{
				\dash\notif::error(T_("System error 3"));
				return false;
			}
		}
	}
}
?>