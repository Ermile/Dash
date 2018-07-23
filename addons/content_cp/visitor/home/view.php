<?php
namespace content_cp\visitor\home;


class view
{
	public static function config()
	{
		$myTitle = T_("Visitor");
		$myDesc  = T_('Check list of visitor and search or filter in them to find your visitor.');

		// add back level to summary link
		$product_list_link =  '<a href="'. \dash\url::here() .'" data-shortkey="121">'. T_('Back to dashboard'). '</a>';
		$myDesc .= ' | '. $product_list_link;

		\dash\data::page_title($myTitle);
		\dash\data::page_desc($myDesc);

		\dash\data::page_pictogram('pinboard');

		$search_string = \dash\request::get('q');
		if($search_string)
		{
			$myTitle .= ' | '. T_('Search for :search', ['search' => $search_string]);
		}

		$args =
		[
			'sort'  => \dash\request::get('sort'),
			'order' => \dash\request::get('order'),
		];

		if(\dash\request::get('status'))
		{
			$args['status'] = \dash\request::get('status');
		}

		if(\dash\request::get('subdomain'))
		{
			$args['subdomain'] = \dash\request::get('subdomain');
		}

		if(\dash\request::get('caller'))
		{
			$args['caller'] = $_GET['caller'];
		}

		if(\dash\request::get('user_id'))
		{
			$args['user_id'] = \dash\request::get('user_id');
		}

		if(\dash\request::get('data'))
		{
			$args['data'] = \dash\request::get('data');
		}

		if(!$args['order'])
		{
			$args['order'] = 'DESC';
		}

		if(!$args['sort'])
		{
			$args['sort'] = 'visitors.id';
		}


		$dataTable = \dash\db\visitors::search(\dash\request::get('q'), $args);

		\dash\data::dataTable($dataTable);

		$filterArray = $args;

		// 'id'            => string '10350' (length=5)
		// 'url'           => string '/cp/log' (length=7)
		// 'host'          => string 'azvir.local' (length=11)
		// 'domain'        => string 'http://haram2.azvir.local' (length=25)
		// 'query'         => string 'q=ByRemem' (length=9)
		// 'urlmd5'        => string '2b7453f9a4dc7dbbf865af77e772fe1c' (length=32)
		// 'pwd'           => string 'http://haram2.azvir.local/cp/log?q=ByRemem' (length=42)
		// 'service_id'    => string '3' (length=1)
		// 'visitor_ip'    => string '2130706433' (length=10)
		// 'url_id'        => string '605' (length=3)
		// 'url_idreferer' => string '604' (length=3)
		// 'agent_id'      => string '1' (length=1)
		// 'user_id'       => string '2' (length=1)
		// 'user_idteam'   => null
		// 'external'      => string '1' (length=1)
		// 'date'          => string '2018-07-23' (length=10)
		// 'time'          => string '19:29:25' (length=8)
		// 'timeraw'       => string '1532357965' (length=10)
		// 'year'          => string '2018' (length=4)
		// 'month'         => string '7' (length=1)
		// 'day'           => string '23' (length=2)
		// 'datemodified'  => null
		// 'ref_url'       => string '/cp/log' (length=7)
		// 'ref_pwd'       => string 'http://haram2.azvir.local/cp/log?q=mem' (length=38)
		// 'ref_host'      => string 'azvir.local' (length=11)
		// 'ref_domain'    => string 'http://haram2.azvir.local' (length=25)

		// set dataFilter
		$dataFilter = \dash\app\sort::createFilterMsg($search_string, $filterArray);
		\dash\data::dataFilter($dataFilter);
	}
}
?>