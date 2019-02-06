<?php
namespace content_cp\home;

class view
{
	public static function config()
	{
		\dash\data::display_cp_posts("content_cp/posts/layout.html");
		\dash\data::display_cpSample("content_cp/sample/layout.html");

		\dash\data::dash_version(\dash\engine\version::get());
		\dash\data::dash_lastUpdate(\dash\utility\git::getLastUpdate());

		\dash\data::page_title(T_('Control Panel'). ' '. \dash\data::site_title());
		\dash\data::page_desc(T_('See all detail about your website in a quick view.'). ' '. T_('You can manage all parts of site from cms and news until user and logs.'));
		\dash\data::page_pictogram('gauge');
		\dash\data::page_special(true);

		$cache = \dash\session::get('cpDashboardCache');
		if(!$cache || true)
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

			$dashboard_detail['latesPost']      = \dash\app\posts::lates_post(['type' => 'post']);
			$dashboard_detail['latesHelp']      = \dash\app\posts::lates_post(['type' => 'help']);
			$dashboard_detail['latesTag']      = \dash\app\term::lates_term(['type' => 'tag']);


			$get_chart                 = [];

			if(\dash\url::subdomain())
			{
				$get_chart['subdomain']    = \dash\url::subdomain();
			}

			$chart                     = [];
			$chart['post']             = \dash\utility\dayevent::chart(['field' => ['news', 'page', 'help', 'attachment']]);
			$dashboard_detail['chart'] = $chart;

			\dash\session::set('cpDashboardCache', $dashboard_detail, null, (60*1));
		}
		else
		{
			$dashboard_detail = \dash\session::get('cpDashboardCache');
		}

		\dash\data::dashboardDetail($dashboard_detail);
		\dash\data::allWordCloud(\dash\app\posts::all_word_cloud());

	}
}
?>