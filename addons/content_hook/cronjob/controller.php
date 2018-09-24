<?php
namespace content_hook\cronjob;


class controller
{
	public static function routing()
	{
		if(\dash\permission::supervisor())
		{
			\content_hook\cronjob\model::post();
			return;
		}

		if(isset($_SERVER['REQUEST_METHOD']) && mb_strtolower($_SERVER['REQUEST_METHOD']) === 'get')
		{
			\dash\header::status(404);
		}

		// if(\dash\url::isLocal())
		// {
		// 	\dash\header::status(404, "Hi Developer :))");
		// 	return;
		// }

		if
		(
			preg_match("/^127\\.0\\.0\\.\d+$/", $_SERVER['SERVER_ADDR']) ||
			(
				isset($_SERVER['REMOTE_ADDR']) &&
				isset($_SERVER['SERVER_ADDR']) &&
				$_SERVER['REMOTE_ADDR'] === $_SERVER['SERVER_ADDR']
			)
		)
		{
			// no thing
		}
		else
		{
			// \dash\utility\telegram::sendMessage("@tejarak_monitor", "#ERROR\n".  json_encode($_SERVER, JSON_UNESCAPED_UNICODE));
			\dash\header::status(404);
		}

	}
}
?>