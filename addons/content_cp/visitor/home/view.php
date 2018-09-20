<?php
namespace content_cp\visitor\home;


class view
{
	public static function config()
	{
		$myTitle = T_("Visitor");
		$myDesc  = T_('Check list of visitor and search or filter in them to find your visitor.');

		$dashboard_detail                  = [];
		$dashboard_detail['visit']         = \dash\app\visitor::total_visit();
		$dashboard_detail['visitor']       = \dash\app\visitor::total_visitor();
		$dashboard_detail['avgtime']       = \dash\app\visitor::total_avgtime();
		$dashboard_detail['maxtrafictime'] = \dash\app\visitor::total_maxtrafictime();
		\dash\data::dashboardDetail($dashboard_detail);
	}
}
?>