<?php
namespace content_cms\visitor\chart;


class view
{
	public static function config()
	{
		$myTitle = T_("Visitor chart");
		$myDesc  = T_('Check list of visitor and search or filter in them to find your visitor.');

		\dash\data::page_title($myTitle);
		\dash\data::page_desc($myDesc);

		$args = [];

		if(\dash\request::get('period'))
		{
			$args['period'] = \dash\request::get('period');
		}

		$file = 'chart';

		if(\dash\request::get('type'))
		{
			$args['type'] = \dash\request::get('type');
			$file         = $args['type'];
		}

		$chart_detail         = [];
		$chart_detail['data'] = \dash\app\visitor::chart($args);
		$chart_detail['file'] = 'content_cms/visitor/chart/'. $file. '.js';

		\dash\data::chartDetail($chart_detail);
	}
}
?>