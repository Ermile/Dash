<?php
namespace content_crm\log\home;


class view
{
	public static function config()
	{
		$myTitle = T_("Log");
		$myDesc  = T_('Check list of log and search or filter in them to find your logs.');

		// add back level to summary link
		$product_list_link =  '<a href="'. \dash\url::here() .'" data-shortkey="121">'. T_('Back to dashboard'). '</a>';
		$myDesc .= ' | '. $product_list_link;

		\dash\data::page_title($myTitle);
		\dash\data::page_desc($myDesc);

		\dash\data::badge_link(\dash\url::this(). '/caller');
		\dash\data::badge_text(T_("Show log caller"));

		\dash\data::page_pictogram('pinboard');

		self::search_log();

	}

	public static function search_log($_args = [])
	{

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

		if($_args && is_array($_args))
		{
			$args = array_merge($args, $_args);
		}

		if(!$args['sort'])
		{
			$args['sort'] = 'id';
		}

		if(!$args['order'])
		{
			$args['order'] = 'desc';
		}

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

		if(\dash\request::get('from'))
		{
			$args['from'] = \dash\request::get('from');
		}

		if(\dash\request::get('to'))
		{
			$args['to'] = \dash\request::get('to');
		}

		if(\dash\request::get('code'))
		{
			$args['code'] = \dash\request::get('code');
		}

		if(\dash\request::get('data'))
		{
			$args['data'] = \dash\request::get('data');
		}

		if(\dash\request::get('datecreated'))
		{
			$args['logs.datecreated'] = \dash\request::get('datecreated');
		}

		if(!$args['order'])
		{
			$args['order'] = 'DESC';
		}

		if(!$args['sort'])
		{
			$args['sort'] = 'id';
		}


		$dataTable = \dash\app\log::list(\dash\request::get('q'), $args);

		\dash\data::dataTable($dataTable);

		$filterArray = $args;

		// set dataFilter
		$dataFilter = \dash\app\sort::createFilterMsg($search_string, $filterArray);
		\dash\data::dataFilter($dataFilter);
	}
}
?>