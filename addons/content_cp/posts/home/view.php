<?php
namespace content_cp\posts\home;


class view
{
	public static function config()
	{

		$moduleTypeTxt = \dash\request::get('type');
		$moduleType    = '';

		if(\dash\request::get('type'))
		{
			$moduleType = '?type='. \dash\request::get('type');
		}

		\dash\data::moduleTypeTxt($moduleTypeTxt);
		\dash\data::moduleType($moduleType);

		$myType = \dash\request::get('type');

		$myTitle = T_("Posts");
		$myDesc  = T_('Check list of posts and search or filter in them to find your posts.'). ' '. T_('Also add or edit specefic post.');
		\dash\data::page_pictogram('pinboard');

		if($myType)
		{
			switch ($myType)
			{
				case 'page':
					\dash\permission::access('cpPageView');
					$myTitle = T_('Pages');
					$myDesc  = T_('Check list of pages and to find your pages.'). ' '. T_('Also add or edit specefic static page.');
					\dash\data::page_pictogram('files-o');
					break;

				case 'post':
				default:
					\dash\permission::access('cpPostsView');
					break;
			}
		}
		else
		{
			\dash\permission::access('cpPostsView');
		}

		// add back level to summary link
		$product_list_link =  '<a href="'. \dash\url::here() .'" data-shortkey="121">'. T_('Back to dashboard'). '</a>';
		$myDesc .= ' | '. $product_list_link;

		\dash\data::page_title($myTitle);
		\dash\data::page_desc($myDesc);

		\dash\data::badge_text(T_('Add new :val', ['val' => $myType]));
		\dash\data::badge_link(\dash\url::this(). '/add'. $moduleType);


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

		if(\dash\request::get('term'))
		{
			$args['term'] = \dash\request::get('term');
		}

		if(!isset($args['status']))
		{
			$args['status'] = ["NOT IN", "('draft', 'deleted')"];
		}

		if(\dash\request::get('type'))
		{
			$args['type'] = \dash\request::get('type');
		}
		else
		{
			$args['type'] = 'post';
		}

		if(!$args['order'])
		{
			$args['order'] = 'DESC';
		}


		if(!$args['sort'])
		{
			$args['sort'] = 'publishdate';
		}

		if(!\dash\permission::check('cpPostsViewAll'))
		{
			$args['user_id'] = \dash\user::id();
		}

		$args['language'] = \dash\language::current();

		\dash\data::sortLink(\content_cp\view::make_sort_link(\dash\app\posts::$sort_field, \dash\url::this()) );
		\dash\data::dataTable(\dash\app\posts::list(\dash\request::get('q'), $args) );

		$filterArray = $args;
		unset($filterArray['language']);
		unset($filterArray['type']);
		unset($filterArray['user_id']);

		if(isset($filterArray['status']))
		{
			if(is_string($filterArray['status']))
			{
				$filterArray[T_("Status")] = $filterArray['status'];
			}
			unset($filterArray['status']);
		}

		// set dataFilter
		$dataFilter = \dash\app\sort::createFilterMsg($search_string, $filterArray);
		\dash\data::dataFilter($dataFilter);
	}
}
?>