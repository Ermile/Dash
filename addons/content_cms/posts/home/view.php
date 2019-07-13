<?php
namespace content_cms\posts\home;


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
					$myTitle = T_('Pages');
					$myDesc  = T_('Check list of pages and to find your pages.'). ' '. T_('Also add or edit specefic static page.');
					\dash\data::page_pictogram('files-o');

					if(\dash\permission::check('cpPageAdd'))
					{
						\dash\data::badge_text(T_('Add new page'));
						\dash\data::badge_link(\dash\url::this(). '/add'. $moduleType);
					}

					break;

				case 'help':
					$myTitle     = T_('Help Center');
					$myDesc      = T_('Check list of article in help center.'). ' '. T_('Also add or edit specefic article.');
					$myBadgeText = T_('Back to list of helps');
					\dash\data::page_pictogram('life-ring');

					if(\dash\permission::check('cpHelpCenterAdd'))
					{
						\dash\data::badge_text(T_('Add new help center article'));
						\dash\data::badge_link(\dash\url::this(). '/add'. $moduleType);
					}
					break;

				case 'post':
					if(\dash\permission::check('cpPostsAdd'))
					{
						\dash\data::badge_text(T_('Add new post'));
						\dash\data::badge_link(\dash\url::this(). '/add'. $moduleType);
					}
					break;

				default:
					$allowPostType = \dash\option::config('allow_post_type');
					if($allowPostType && is_array($allowPostType))
					{
						if(in_array($myType, $allowPostType))
						{
							// no problem
						}
						else
						{
							\dash\header::status(404);
						}
					}
					else
					{
						\dash\header::status(404);
					}
					\dash\data::badge_text(T_('Add new post'));
					\dash\data::badge_link(\dash\url::this(). '/add'. $moduleType);
					break;
			}
		}
		else
		{
			if(\dash\permission::check('cpPostsAdd'))
			{
				\dash\data::badge_text(T_('Add new post'));
				\dash\data::badge_link(\dash\url::this(). '/add'. $moduleType);
			}
		}

		\dash\data::listSpecial(\dash\app\posts\special::list());
		// add back level to summary link

		\dash\data::page_title($myTitle);
		\dash\data::page_desc($myDesc);


		\dash\data::badge2_text(T_('Back to dashboard'));
		\dash\data::badge2_link(\dash\url::here());


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

		// if(!isset($args['status']))
		// {
		// 	$args['status'] = ["NOT IN", "('draft', 'deleted')"];
		// }

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


		if(\dash\request::get('special'))
		{
			$args['special'] = \dash\request::get('special');
		}

		if($myType === 'attachment')
		{
			// no check lang
		}
		else
		{
			$args['language'] = \dash\language::current();
		}

		\dash\data::sortLink(\content_cms\view::make_sort_link(\dash\app\posts::$sort_field, \dash\url::this()) );
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

		// get post count group by status
		$postCounter = \dash\app\posts::get_post_counter($args);
		\dash\data::postCounter($postCounter);
	}
}
?>