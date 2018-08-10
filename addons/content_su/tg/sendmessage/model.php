<?php
namespace content_su\tg\sendmessage;

class model
{
	public static function post()
	{
		$chatid   = \dash\request::post('chatid');
		$text     = \dash\request::post('text');

		$myData   = ['chat_id' => $chatid, 'text' => $text];
		$myResult = \dash\social\telegram\tg::sendMessage($myData);

		\dash\session::set('tg_send', json_encode(, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
		\dash\session::set('tg_response', json_encode($myResult, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

		\dash\redirect::pwd();
	}
}
?>