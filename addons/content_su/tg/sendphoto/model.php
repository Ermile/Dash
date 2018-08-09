<?php
namespace content_su\tg\sendphoto;

class model
{
	public static function post()
	{
		$chatid   = \dash\request::post('chatid');
		$text     = \dash\request::post('text');
		$fileData = "https://ermile.com/static/images/logo.png";
		$fileData = \dash\app\file::upload_quick('file1');



		$myData   = ['chat_id' => $chatid, 'photo' => $fileData, 'caption' => $text];
		$myResult = \dash\social\telegram\tg::sendPhoto($myData);

		\dash\session::set('tg_send', $myData);
		\dash\session::set('tg_response', $myResult);

		\dash\redirect::pwd();
	}
}
?>