<?php
namespace content_su\home;

class controller
{
	public static function routing()
	{
		if(\dash\request::get('server') === 'status')
		{
			$serverDetail =
			[
				'cpu'    => \dash\utility\server::getServerLoad(),
				'memory' => rand(50,100),
				'disk'   => rand(10,30),
				'time'   => date('H:i:s')
			];

			echo json_encode($serverDetail, JSON_UNESCAPED_UNICODE);
			\dash\code::exit();
		}

	}
}
?>