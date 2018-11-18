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
			if(time() - $start > 20)
			{
				break;
			}

			if(isset($value['telegram']))
			{
				$telegram = json_decode($value['telegram'], true);

				if($telegram)
				{
					self::send_telegram($telegram);
				}
			}

			if(isset($value['sms']))
			{
				$sms = json_decode($value['sms'], true);
				if(isset($sms['mobile']) && isset($sms['text']))
				{
					self::send_sms($sms['mobile'], $sms['text']);
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
		if(isset($_data['method']))
		{
			$method   = $_data['method'];
			unset($_data['method']);
			$myResult = \dash\social\telegram\tg::$method($_data);
		}
		else
		{
			$myResult = \dash\social\telegram\tg::sendMessage($_data);
		}

	}


	private static function send_sms($_mobile, $_text)
	{
		if(\dash\url::isLocal())
		{
			return false;
		}

		if(!\dash\option::sms('kavenegar', 'status'))
		{
			return false;
		}
		else
		{
			\dash\utility\sms::send($_mobile, $_text);
		}
	}
}
?>
