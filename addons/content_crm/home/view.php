<?php
namespace content_crm\home;

class view
{
	public static function config()
	{

		\dash\data::dash_version(\dash\engine\version::get());
		\dash\data::dash_lastUpdate(\dash\utility\git::getLastUpdate());

		\dash\data::page_title(T_('Control Panel'). ' '. \dash\data::site_title());
		\dash\data::page_desc(T_('See all detail about your website in a quick view.'). ' '. T_('You can manage all parts of site from cms and news until user and logs.'));
		\dash\data::page_pictogram('gauge');
		\dash\data::page_special(true);

		$cache = \dash\session::get('crmDashboardCache');
		if(!$cache || true)
		{

			$dashboard_detail                 = [];
			$dashboard_detail['users']        = \dash\db\users::get_count();
			$dashboard_detail['activeUser']   = \dash\db\users::get_count(['status' => 'active']);
			$dashboard_detail['permissions']  = count(\dash\permission::groups());
			$dashboard_detail['logs']         = \dash\db\logs::get_count();
			$dashboard_detail['latestLogs']   = \dash\app\log::lates_log(['caller' => 'userLogin']);
			$dashboard_detail['latestMember'] = \dash\app\user::lates_user();
			$dashboard_detail['latestTicket'] = \dash\app\ticket::lates_ticket();

			$get_chart                        = [];

			$chart                            = [];
			$chart['gender']                  = \dash\app\user::chart_gender($get_chart);
			$chart['status']                  = \dash\app\user::chart_status($get_chart);
			$chart['identify']                = \dash\app\user::chart_identify($get_chart);


			$dashboard_detail['chart'] = $chart;

			\dash\session::set('crmDashboardCache', $dashboard_detail, null, (60*1));
		}
		else
		{
			$dashboard_detail = \dash\session::get('crmDashboardCache');
		}

		\dash\data::dashboardDetail($dashboard_detail);
	}
}
?>