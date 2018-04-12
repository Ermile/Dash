<?php
namespace content_su\sendnotify;

class view
{
	public static function config()
	{

		$mobile_or_id = \dash\request::get('user');
		if($mobile_or_id)
		{
			\dash\data::userInfo(\content_su\sendnotify\model::connection_way($mobile_or_id));
		}

		$send_notify_text = \dash\session::get('send_notify_text');

		if($send_notify_text)
		{
			\dash\data::sendNotify_text($send_notify_text);
		}

	}
}
?>