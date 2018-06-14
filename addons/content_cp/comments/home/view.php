<?php
namespace content_cp\comments\home;


class view
{
	public static function config()
	{
		\dash\permission::access('cpCommentsView');

		\dash\data::page_title(T_("Comments"));
		\dash\data::page_desc(T_('Check list of comments and search or filter in them to find your comments.'). ' '. T_('Also add or edit specefic comments.'));
		\dash\data::page_pictogram('comments');

		// add back level to summary link
		\dash\data::badge2_text(T_('Back to dashboard'));
		\dash\data::badge2_link(\dash\url::here());


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

		$args['type'] = 'comment';

		if(!$args['order'])
		{
			$args['order'] = 'DESC';
		}

		if(!$args['sort'])
		{
			$args['sort'] = 'id';
		}

		\dash\data::sortLink(\content_cp\view::make_sort_link(\dash\app\comment::$sort_field, \dash\url::this()));
		\dash\data::dataTable(\dash\app\comment::list(\dash\request::get('q'), $args));

		$filterArray = $args;
		unset($filterArray['type']);

		// set dataFilter
		$dataFilter = \dash\app\sort::createFilterMsg($search_string, $filterArray);
		\dash\data::dataFilter($dataFilter);

	}
}
?>