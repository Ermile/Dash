<?php
namespace content_support\ticket\report;

class controller
{
	public static function routing()
	{
		\dash\permission::access('supportTicketReport');

		if(\dash\request::get('ajaxreport') === 'json')
		{
			echo \dash\app\ticket::chart_ticket();
			\dash\code::boom();
		}
	}
}
?>
