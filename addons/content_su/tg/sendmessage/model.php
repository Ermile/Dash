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

		\dash\session::set('tg_send', $myData);
		\dash\session::set('tg_response', $myResult);

		\dash\redirect::pwd();
	}
}
?>