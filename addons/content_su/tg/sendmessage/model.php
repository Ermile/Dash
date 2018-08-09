<?php
namespace content_su\tg\sendmessage;

class model
{
	public static function post()
	{
		$chatid = \dash\request::post('chatid');
		$text   = \dash\request::post('text');

		// var_dump($chatid);
		// var_dump($text);

		$myData = ['chat_id' => $chatid, 'text' => $text];
		$result = \dash\social\telegram\tg::sendMessage($myData);
		var_dump($myData);
		var_dump($result);
	}
}
?>