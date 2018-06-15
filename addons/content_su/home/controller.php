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
				'memory' => \dash\utility\server::getServerLoad()+10,
				'disk'   => \dash\utility\server::getServerLoad()+20
			];

			echo json_encode($serverDetail);
			\dash\code::exit();
		}

	}
}
?>