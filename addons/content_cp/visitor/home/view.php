<?php
namespace content_cp\visitor\home;


class view
{
	public static function config()
	{
		$myTitle = T_("Visitor");
		$myDesc  = T_('Check list of visitor and search or filter in them to find your visitor.');


		\dash\data::page_title($myTitle);
		\dash\data::page_desc($myDesc);

		$args = [];
		if(\dash\request::get('period'))
		{
			$args['period'] = \dash\request::get('period');
		}

		$dashboard_detail                  = [];
		$dashboard_detail['visit']         = \dash\app\visitor::total_visit($args);
		$dashboard_detail['visitor']       = \dash\app\visitor::total_visitor($args);
		$dashboard_detail['avgtime']       = \dash\app\visitor::total_avgtime($args);
		$dashboard_detail['maxtrafictime'] = \dash\app\visitor::total_maxtrafictime($args);
		$dashboard_detail['visitorchart']  = \dash\app\visitor::chart_visitorchart($args);
		\dash\data::dashboardDetail($dashboard_detail);
	}
}
?>