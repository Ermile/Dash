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
		if(!$cache)
		{

			$dashboard_detail                   = [];
			$dashboard_detail['news']           = \dash\db\posts::get_count(['type' => 'post']);
			$dashboard_detail['pages']          = \dash\db\posts::get_count(['type' => 'page']);
			$dashboard_detail['cats']           = \dash\db\terms::get_count(['type' => 'cat']);
			$dashboard_detail['tags']           = \dash\db\terms::get_count(['type' => 'tag']);
			$dashboard_detail['helpcenter']     = \dash\db\posts::get_count(['type' => 'help']);
			$dashboard_detail['helpcentertags'] = \dash\db\terms::get_count(['type' => 'help_tag']);
			$dashboard_detail['supporttags']    = \dash\db\terms::get_count(['type' => 'support_tag']);
			$dashboard_detail['tickets']        = \dash\db\comments::get_count(['type' => 'ticket', 'parent' => null]);
			$dashboard_detail['users']          = \dash\db\users::get_count();
			$dashboard_detail['permissions']    = count(\dash\permission::groups());
			$dashboard_detail['logs']           = \dash\db\logs::get_count();
			$dashboard_detail['comments']       = \dash\db\comments::get_count(['type' => 'comments']);
			$dashboard_detail['visit']          = \dash\db\visitors::get_count();

			$get_chart = [];

			if(\dash\url::subdomain())
			{
				$get_chart['subdomain'] = \dash\url::subdomain();
			}

			$chart             = [];
			$chart['gender']   = \dash\app\user::chart_gender($get_chart);
			$chart['status']   = \dash\app\user::chart_status($get_chart);
			$chart['identify'] = \dash\app\user::chart_identify($get_chart);


			$dashboard_detail['chart'] = $chart;

			\dash\session::set('crmDashboardCache', $dashboard_detail, null, (60*1));
		}
		else
		{
			$dashboard_detail = \dash\session::get('crmDashboardCache');
		}
		// var_dump($dashboard_detail);exit();

		\dash\data::dashboardDetail($dashboard_detail);
		// $this->data->page['title']       = T_(ucfirst( str_replace('/', ' ', \dash\url::directory()) ));

		// $this->data->dir['right']     = $this->global->direction == 'rtl'? 'left':  'right';
		// $this->data->dir['left']      = $this->global->direction == 'rtl'? 'right': 'left';
	}
}
?>