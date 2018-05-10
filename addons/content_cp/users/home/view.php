<?php
namespace content_cp\users\home;

class view
{

	public static function config()
	{
		\dash\permission::access('cpUsersView');

		\dash\data::page_title(T_('List of users'));
		\dash\data::page_desc(T_('Some detail about your users!'));
		\dash\data::page_desc(T_('Check list of users and search or filter in them to find your user.'));
		\dash\data::page_desc(\dash\data::page_desc(). ' '. T_('Also add or edit specefic user.'));

		// add back level to summary link
		\dash\data::badge_link(\dash\url::this(). '/set');
		\dash\data::badge_text(T_('Add new user'));

		$search_string            = \dash\request::get('q');
		if($search_string)
		{
			\dash\data::page_title(\dash\data::page_title(). ' | '. T_('Search for :search', ['search' => $search_string]));
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

		if(\dash\request::get('permission'))
		{
			$args['permission'] = \dash\request::get('permission');
		}

		$sortLink = \content_cp\view::make_sort_link(\dash\app\user::$sort_field, \dash\url::this());
		$dataTable = \dash\app\user::list(\dash\request::get('q'), $args);

		\dash\data::sortLink($sortLink);
		\dash\data::dataTable($dataTable);

		$check_empty_datatable = $args;
		unset($check_empty_datatable['sort']);
		unset($check_empty_datatable['order']);

		// set dataFilter
		$dataFilter = \content_cp\view::createFilterMsg($search_string, $check_empty_datatable);
		\dash\data::dataFilter($dataFilter);


	}
}
?>