<?php
namespace content_cms\visitor\home;


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
		$dashboard_detail['chart']         = \dash\app\visitor::chart_visitorchart($args);

		\dash\data::dashboardDetail($dashboard_detail);
		\dash\data::alexa(self::alexaRank());

	}


	public static function alexaRank($_website = null)
	{
		if(!$_website)
		{
			$_website = \dash\url::domain();
			if(\dash\url::tld() === 'local')
			{
				$_website = \dash\url::root(). '.com';
			}
		}
		$apiURL = 'http://data.alexa.com/data?cli=10&dat=snbamz&url='. $_website;
		$alexaResult = simplexml_load_file($apiURL);
		$alexaRank = '-';
		if(isset($alexaResult->SD[1]->POPULARITY))
		{
			$alexaRank = $alexaResult->SD[1]->POPULARITY->attributes()->TEXT;
			$alexaRank = intval($alexaRank);
		}
		$result =
		[
			'website' => $_website,
			'rank'    => $alexaRank,
			'api'     => $apiURL,
			'url'     => 'https://www.alexa.com/siteinfo/'. $_website
		];

		return $result;
	}
}
?>