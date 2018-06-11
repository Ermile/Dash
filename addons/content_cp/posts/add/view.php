<?php
namespace content_cp\posts\add;

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

		\dash\data::page_pictogram('file-text-o');

		\dash\data::moduleTypeTxt($moduleTypeTxt);
		\dash\data::moduleType($moduleType);
		\dash\data::listCats(\dash\app\term::cat_list());

		$pageList = \dash\db\posts::get(['type' => 'page', 'language' => \dash\language::current(), 'status' => ["NOT IN", "('deleted')"]]);
		$pageList = array_map(['\dash\app\posts', 'ready'], $pageList);
		\dash\data::pageList($pageList);


		$myTitle   = T_("Add new post");
		$myDesc    = T_("Posts can contain keyword and category with title and descriptions.");

		$myBadgeLink = \dash\url::this(). $moduleType;
		$myBadgeText = T_('Back to list of posts');

		$myType = \dash\request::get('type');
		if($myType)
		{
			switch ($myType)
			{
				case 'page':

					\dash\permission::access('cpPageAdd');

					$myTitle = T_('Add new page');
					$myDesc  = T_("Add new static page like about or honors");

					$myBadgeText = T_('Back to list of pages');
					break;

				case 'post':
				default:
					\dash\permission::access('cpPostsAdd');
					break;
			}
		}
		else
		{
			\dash\permission::access('cpPostsAdd');
		}

		\dash\data::page_title($myTitle);
		\dash\data::page_desc($myDesc);

		\dash\data::badge_text($myBadgeText);
		\dash\data::badge_link($myBadgeLink);
	}
}
?>