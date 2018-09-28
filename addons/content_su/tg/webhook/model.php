<?php
namespace content_su\tg\webhook;

class model
{
	public static function post()
	{
		$url             = \dash\request::post('url');
		$max_connections = \dash\request::post('max_connections');

		$myData   = ['url' => $url, 'max_connections' => $max_connections];
		$myResult = \dash\social\telegram\tg::json_setWebhook($myData);
		\dash\log::set('tgSetWebhook', ['url' => $url, 'max_connections' => $max_connections]);

		\dash\session::set('tg_send', json_encode($myData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
		\dash\session::set('tg_response', $myResult);

		\dash\redirect::pwd();
	}
}
?>