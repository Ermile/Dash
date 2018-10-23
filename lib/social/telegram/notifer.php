<?php
namespace dash\social\telegram;

class notifer
{
	public static function check()
	{
		$myNotif    = \dash\notif::get();
		$myNotifMsg = null;
		if(isset($myNotif['msg']))
		{
			$myNotifMsg = $myNotif['msg'];
		}

		if($myNotifMsg)
		{
			foreach ($myNotifMsg as $_index => $_msg)
			{
				if(isset($_msg['text']))
				{
					$msgText = $_msg['text'];
					// send message to show this text
				}
				if(isset($_msg['type']))
				{
					switch ($_msg['type'])
					{
						case 'ok':
							$msgText = '✅ '. $msgText;
							break;

						case 'info':
							$msgText = '💠 '. $msgText;
							break;

						case 'warn':
							$msgText = '⚠️ '. $msgText;
							break;

						case 'error':
							$msgText = '❗️ '. $msgText;
							break;
					}

				}
				if($msgText)
				{
					tg::sendMessage($msgText);
				}
			}
			\dash\notif::clean();
		}
	}
}
?>