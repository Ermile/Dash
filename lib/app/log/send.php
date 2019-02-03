<?php
namespace dash\app\log;


class send
{


	public static function notification()
	{

		$not_send = \dash\db\logs::get(['notif' => 1, 'send' => null]);
		if(!$not_send || !is_array($not_send))
		{
			// nothing to send
			return true;
		}

		$start = time();

		foreach ($not_send as $key => $value)
		{
			if(!\dash\app\log\protector::cronjob($start))
			{
				break;
			}

			if(isset($value['telegram']))
			{
				$telegram = json_decode($value['telegram'], true);

				if($telegram)
				{
					if(!\dash\app\log\protector::tg($telegram))
					{
						break;
					}
					self::send_telegram($telegram);
				}
			}

			if(isset($value['sms']))
			{
				$sms = json_decode($value['sms'], true);

				if(isset($sms['mobile']) && isset($sms['text']))
				{
					$meta = [];
					if(isset($sms['meta']))
					{
						$meta = $sms['meta'];
					}

					self::send_sms($sms['mobile'], $sms['text'], $meta);
				}
			}

			\dash\db\logs::update(['send' => 1], $value['id']);
		}
	}


	private static function send_telegram($_data)
	{
		if(!\dash\option::social('telegram', 'status'))
		{
			return false;
		}

		foreach ($_data as $key => $value)
		{
			if(isset($value['method']))
			{
				$method   = $value['method'];
				unset($value['method']);
				$myResult = \dash\social\telegram\tg::$method($value);
			}
			else
			{
				$myResult = \dash\social\telegram\tg::sendMessage($value);
			}
		}

	}


	private static function send_sms($_mobile, $_text, $_meta = [])
	{
		if(\dash\url::isLocal())
		{
			return false;
		}

		\dash\utility\sms::send($_mobile, $_text, $_meta);

	}
}
?>
