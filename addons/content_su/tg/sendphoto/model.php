<?php
namespace content_su\tg\sendphoto;

class model
{
	public static function post()
	{
		$chatid = \dash\request::post('chatid');
		$text   = \dash\request::post('text');
		// $file   = \dash\app\file::upload_quick('file1');
		$file   = \dash\request::files('file1');
		$file   = file_get_contents(\dash\request::files('file1')['tmp_name']);

		$myData   = ['chat_id' => $chatid, 'photo' => $file, 'caption' => $text];
		$myResult = \dash\social\telegram\tg::sendMessage($myData);

		\dash\session::set('tg_send', $myData);
		\dash\session::set('tg_response', $myResult);

		\dash\redirect::pwd();
	}
}
?>