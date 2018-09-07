<?php
namespace dash\app;


class sendnotification
{

	public static function send()
	{
		$list = \dash\db\sendnotifications::not_sended();
		if(!$list)
		{
			return;
		}

		$send_telegram = [];
		$send_sms      = [];
		$send_email    = [];


		foreach ($list as $key => $value)
		{
			switch ($value['way'])
			{
				case 'telegram':
					$send_telegram[] = $value;
					break;

				case 'sms':
					$send_sms[] = $value;
					break;

				case 'email':
					$send_email[] = $value;
					break;

			}
		}

		// 'awaiting','sended','expire','cancel','cannotsend', 'turnoff'

		if(!empty($send_telegram))
		{
			if(!\dash\option::social('telegram', 'status'))
			{
				\dash\db\sendnotifications::set_status('turnoff', array_column($send_telegram, 'id'));
			}
			else
			{
				\dash\db\sendnotifications::set_status('sended', array_column($send_telegram, 'id'));
				foreach ($send_telegram as $key => $value)
				{
					$myData   = ['chat_id' => $value['to'], 'text' => $value['text']];
					$myResult = \dash\social\telegram\tg::json_sendMessage($myData);
				}
			}
		}

		if(!empty($send_sms))
		{
			if(!\dash\option::social('kavenegar', 'status'))
			{
				\dash\db\sendnotifications::set_status('turnoff', array_column($send_sms, 'id'));
			}
			else
			{
				\dash\db\sendnotifications::set_status('sended', array_column($send_sms, 'id'));
				foreach ($send_sms as $key => $value)
				{
					\dash\utility\sms::send($value['to'], $value['text']);
				}
			}
		}
	}
}
?>