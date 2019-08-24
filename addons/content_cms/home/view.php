<?php
namespace content_cms\home;

class view
{

	private static function fix_file_table()
	{
		if(\dash\request::get('fixfile'))
		{
			$query = "SELECT * FROM files WHERE files.status = 'draft' ";
			$result = \dash\db::get($query);
			if(is_array($result) && $result)
			{
				$multi_query = [];
				foreach ($result as $key => $value)
				{
					$temp           = [];
					$myUrl = preg_match("/^files\/(\d+)\/.*$/", $value['url'], $c);
					if($myUrl && isset($c[1]))
					{
						$temp['folder'] = $c[1];
					}
					$temp['url']    = \dash\url::site(). '/'. $value['path'];
					$temp['status'] = null;
					$set = \dash\db\config::make_set($temp);
					$multi_query[] = " UPDATE files SET $set WHERE files.id = $value[id] LIMIT 1 ";
				}

				if(!empty($multi_query))
				{
					$multi_query = implode(';', $multi_query);
					$result = \dash\db::query($multi_query, true, ['multi_query' => true]);
				}
			}
		}
	}


	public static function config()
	{
		self::fix_file_table();

		\dash\data::display_cp_posts("content_cms/posts/layout.html");
		\dash\data::display_cpSample("content_cms/sample/layout.html");

		\dash\data::dash_version(\dash\engine\version::get());
		\dash\data::dash_lastUpdate(\dash\utility\git::getLastUpdate());

		\dash\data::page_title(T_('Control Panel'). ' '. \dash\data::site_title());
		\dash\data::page_desc(T_('See all detail about your website in a quick view.'). ' '. T_('You can manage all parts of site from cms and news until user and logs.'));
		\dash\data::page_pictogram('gauge');
		\dash\data::page_special(true);

		self::dashboard_detail();
		self::dashboard_detail_no_lang();
		self::all_word_cloud();
	}


	private static function makeDashboardArgs($_args)
	{
		if(!\dash\option::config('no_subdomain'))
		{
			$subdomain = \dash\url::subdomain();
			if($subdomain)
			{
				$_args['subdomain'] = $subdomain;
			}
			else
			{
				$_args['subdomain'] = null;
			}
		}

		return $_args;
	}



	private static function dashboard_detail()
	{
		$dashboard_detail = \dash\session::get('cpDashboardCache_'. \dash\language::current());
		if(!$dashboard_detail)
		{
			$dashboard_detail                   = [];
			$dashboard_detail['news']           = \dash\db\posts::get_count(self::makeDashboardArgs(['language' => \dash\language::current(), 'type' => 'post']));
			$dashboard_detail['pages']          = \dash\db\posts::get_count(self::makeDashboardArgs(['language' => \dash\language::current(), 'type' => 'page']));
			$dashboard_detail['cats']           = \dash\db\terms::get_count(self::makeDashboardArgs(['language' => \dash\language::current(), 'type' => 'cat']));
			$dashboard_detail['tags']           = \dash\db\terms::get_count(self::makeDashboardArgs(['language' => \dash\language::current(), 'type' => 'tag']));
			$dashboard_detail['helpcenter']     = \dash\db\posts::get_count(self::makeDashboardArgs(['language' => \dash\language::current(), 'type' => 'help']));
			$dashboard_detail['helpcentertags'] = \dash\db\terms::get_count(self::makeDashboardArgs(['language' => \dash\language::current(), 'type' => 'help_tag']));
			$dashboard_detail['supporttags']    = \dash\db\terms::get_count(self::makeDashboardArgs(['language' => \dash\language::current(), 'type' => 'support_tag']));
			$dashboard_detail['tickets']        = \dash\db\comments::get_count(self::makeDashboardArgs(['type' => 'ticket', 'parent' => null]));
			$dashboard_detail['latesPost']      = \dash\app\posts::lates_post(self::makeDashboardArgs(['language' => \dash\language::current(), 'type' => 'post']));
			$dashboard_detail['latesHelp']      = \dash\app\posts::lates_post(self::makeDashboardArgs(['language' => \dash\language::current(), 'type' => 'help']));
			$dashboard_detail['latesTag']      = \dash\app\term::lates_term(self::makeDashboardArgs(['language' => \dash\language::current(), 'type' => 'tag']));


			$chart                     = [];
			$chart['post']             = \dash\utility\dayevent::chart(['field' => ['news', 'page', 'help', 'attachment']]);
			$dashboard_detail['chart'] = $chart;

			\dash\session::set('cpDashboardCache_'. \dash\language::current(), $dashboard_detail, null, (60*1));
		}

		\dash\data::dashboardDetail($dashboard_detail);
	}


	private static function dashboard_detail_no_lang()
	{

		$dashboard_detail_no_lang = \dash\session::get('cpDashboardCacheNoLang_'. \dash\language::current());
		if(!$dashboard_detail_no_lang)
		{
			$dashboard_detail_no_lang                   = [];
			$dashboard_detail_no_lang['news']           = \dash\db\posts::get_count(self::makeDashboardArgs(['type' => 'post']));
			$dashboard_detail_no_lang['pages']          = \dash\db\posts::get_count(self::makeDashboardArgs(['type' => 'page']));
			$dashboard_detail_no_lang['cats']           = \dash\db\terms::get_count(self::makeDashboardArgs(['type' => 'cat']));
			$dashboard_detail_no_lang['tags']           = \dash\db\terms::get_count(self::makeDashboardArgs(['type' => 'tag']));
			$dashboard_detail_no_lang['helpcenter']     = \dash\db\posts::get_count(self::makeDashboardArgs(['type' => 'help']));
			$dashboard_detail_no_lang['helpcentertags'] = \dash\db\terms::get_count(self::makeDashboardArgs(['type' => 'help_tag']));
			$dashboard_detail_no_lang['supporttags']    = \dash\db\terms::get_count(self::makeDashboardArgs(['type' => 'support_tag']));
			$dashboard_detail_no_lang['latesPost']      = \dash\app\posts::lates_post(self::makeDashboardArgs(['type' => 'post']));
			$dashboard_detail_no_lang['latesHelp']      = \dash\app\posts::lates_post(self::makeDashboardArgs(['type' => 'help']));
			$dashboard_detail_no_lang['latesTag']      = \dash\app\term::lates_term(self::makeDashboardArgs(['type' => 'tag']));
			\dash\session::set('cpDashboardCacheNoLang_'. \dash\language::current(), $dashboard_detail_no_lang, null, (60*1));
		}

		\dash\data::dashboardDetailNoLang($dashboard_detail_no_lang);
	}


	private static function all_word_cloud()
	{
		$args             = [];
		$args['language'] = \dash\language::current();
		$args             = self::makeDashboardArgs($args);

		$allWordCloud = \dash\utility\catch_file::get('cpWordCload_'. \dash\url::subdomain(), false);
		if(!$allWordCloud)
		{
			$allWordCloud = \dash\app\posts::all_word_cloud($args);
			\dash\utility\catch_file::set('cpWordCload_'. \dash\url::subdomain(), $allWordCloud, 60*2);
		}

		\dash\data::allWordCloud($allWordCloud);
	}
}
?>