<?php
namespace dash\app\log;


class send
{
	private static $isSended  = [];
	private static $logUpdate = [];


	public static function notification()
	{
		$not_send = \dash\db\logs::get(['notif' => 1, 'send' => null]);
		if(!$not_send || !is_array($not_send))
		{
			// nothing to send
			return true;
		}

		\dash\temp::set('logLoadUserDetail', true);

		$not_send = array_map(['\dash\app\log', 'ready'], $not_send);

		$send_telegram = [];
		$send_sms      = [];
		$send_email    = [];

		foreach ($not_send as $key => $value)
		{
			if(isset($value['telegram']) && $value['telegram'])
			{
				$send_telegram[] = $value;
			}

			if(isset($value['sms']) && $value['sms'])
			{
				$send_sms[] = $value;
			}

			if(isset($value['email']) && $value['email'])
			{
				$send_email[] = $value;
			}
		}

		if(!empty($send_telegram))
		{
			self::send_by_telegram($send_telegram);
		}

		if(!empty($send_sms))
		{
			self::send_by_sms($send_sms);
		}

		\dash\db\logs::save_temp_update();
	}


	private static function sended($_id, $_user, $_check = false)
	{
		if($_check)
		{
			return isset(self::$isSended[$_id][$_user]);
		}
		else
		{
			self::$isSended[$_id][$_user] = true;
		}
	}


	private static function send_by_telegram($_array)
	{
		if(!\dash\option::social('telegram', 'status'))
		{
			$id_raw = array_column($_array, 'id_raw');
			if(!empty($id_raw))
			{
				$id_raw = implode(',', $id_raw);
				\dash\db\logs::update_where(['send' => 0],['id' => ["IN", "($id_raw)"]]);
			}
		}
		else
		{
			$start_time = time();
			$count_send = 0;
			foreach ($_array as $key => $value)
			{
				if(!isset($value['user_detail']) || (isset($value['user_detail']) && !is_array($value['user_detail'])))
				{
					\dash\db\logs::update_temp(['send' => 0], $value['id_raw']);
					continue;
				}

				$is_sended = false;

				foreach ($value['user_detail'] as $user_id => $user_detail)
				{
					if(isset($user_detail['chatid']) && isset($value['send_msg']['telegram']))
					{
						// check to not send duplicate msg to one user
						if(self::sended($value['id_raw'], $user_id, true))
						{
							continue;
						}

						$myData =
						[
							'text'         => strip_tags($value['send_msg']['telegram']),
							'reply_markup' => false,
							'chat_id'      => $user_detail['chatid'],
						];

						if(isset($value['btn']['telegram']) && is_array($value['btn']['telegram']))
						{
							$myData = array_merge($myData, $value['btn']['telegram']);
						}

						$myData = \dash\app\log::myT_($myData, $value);

						$myResult = false;

						if(isset($value['send_gif']) && $value['send_gif'] && isset($value['gif_url']))
						{
							$myData['caption'] = $myData['text'];
							unset($myData['text']);
							$myData['document'] = $value['gif_url'];

							$myResult = \dash\social\telegram\tg::sendDocument($myData);
						}
						else
						{
							$myResult = \dash\social\telegram\tg::sendMessage($myData);
						}

						// $myResult       = [];
						// $myResult['ok'] = 1;

						if(isset($myResult['ok']) && $myResult['ok'])
						{
							// if can send to the user tg not send in other way
							self::sended($value['id_raw'], $user_id);
							$is_sended = true;
							\dash\db\logs::update_temp(['send' => 1], $value['id_raw']);
						}

						$count_send++;

						if((time() - $start_time) > 60 || $count_send > 20)
						{
							return false;
						}
					}
				}
			}
		}
	}


	private static function send_by_sms($_array)
	{
		if(!\dash\option::sms('kavenegar', 'status'))
		{
			$id_raw = array_column($_array, 'id_raw');
			if(!empty($id_raw))
			{
				$id_raw = implode(',', $id_raw);
				\dash\db\logs::update_where(['send' => 0],['id' => ["IN", "($id_raw)"]]);
			}
		}
		else
		{
			$start_time = time();
			$count_send = 0;
			$is_sended  = false;
			foreach ($_array as $key => $value)
			{
				if(!isset($value['user_detail']) || (isset($value['user_detail']) && !is_array($value['user_detail'])))
				{
					\dash\db\logs::update_temp(['send' => 0], $value['id_raw']);
					continue;
				}

				\dash\db\logs::update_temp(['send' => 1], $value['id_raw']);

				foreach ($value['user_detail'] as $user_id => $user_detail)
				{
					if(isset($user_detail['mobile']) && isset($value['send_msg']['sms']))
					{
						// check to not send duplicate msg to one user
						if(self::sended($value['id_raw'], $user_id, true))
						{
							continue;
						}
						self::sended($value['id_raw'], $user_id);

						if(\dash\url::isLocal())
						{
							continue;
						}

						\dash\utility\sms::send($user_detail['mobile'], $value['send_msg']['sms'], $value['send_msg']);

						// @check need to check the telegram is send this message or not

						$count_send++;

						if((time() - $start_time) > 60 || $count_send > 20)
						{
							return false;
						}
					}
				}
			}
		}
	}
}
?>
