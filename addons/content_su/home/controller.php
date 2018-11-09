<?php
namespace content_su\home;

class controller
{
	public static function routing()
	{
		if(\dash\request::get('cmd') === 'health')
		{
			\dash\temp::set('force_stop_visitor', true);
			$serverDetail =
			[
				'cpu'    => \dash\utility\server::cpu_usage(),
				'memory' => \dash\utility\server::memory_usage(),
				'disk'   => \dash\utility\server::disk_usage(),
				'time'   => date('H:i:s')
			];

			echo json_encode($serverDetail, JSON_UNESCAPED_UNICODE);
			\dash\code::boom();
		}

	}
}
?>