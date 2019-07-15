<?php
namespace content_cms\attachment\home;


class view
{
	public static function config()
	{

		\dash\data::page_title(T_("Attachments"));

		\dash\data::badge_text(T_('Add new attachment'));
		\dash\data::badge_link(\dash\url::this(). '/add');

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


		if(!$args['order'])
		{
			$args['order'] = 'DESC';
		}


		if(!$args['sort'])
		{
			$args['sort'] = 'id';
		}

		\dash\data::sortLink(\content_cms\view::make_sort_link(\dash\app\file::$sort_field, \dash\url::this()) );
		$dataTable = \dash\app\file::list(\dash\request::get('q'), $args);

		\dash\data::dataTable($dataTable );

		// set dataFilter
		// $dataFilter = \dash\app\sort::createFilterMsg($search_string, $filterArray);
		// \dash\data::dataFilter($dataFilter);
	}
}
?>