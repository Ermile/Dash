<?php
namespace content_cms\posts\add;

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
					$myTitle = T_('Add new page');
					$myDesc  = T_("Add new static page like about or honors");
					$myBadgeText = T_('Back to list of pages');
					$pageList = \dash\db\posts::get(['type' => 'page', 'language' => \dash\language::current(), 'status' => ["NOT IN", "('deleted')"]]);
					$pageList = array_map(['\dash\app\posts', 'ready'], $pageList);
					\dash\data::pageList($pageList);
					break;

				case 'help':
					$myTitle     = T_('Add new help');
					$myDesc      = T_("Add new article to help center or new faq.");
					$myBadgeText = T_('Back to list of helps');
					$pageList    = \dash\db\posts::get(['type' => 'help', 'language' => \dash\language::current(), 'status' => ["NOT IN", "('deleted')"]]);
					$pageList    = array_map(['\dash\app\posts', 'ready'], $pageList);
					\dash\data::pageList($pageList);
					break;

				case 'post':
				default:
					\dash\data::listCats(\dash\app\term::cat_list());
					break;
			}
		}
		else
		{
			\dash\data::listCats(\dash\app\term::cat_list());
		}


		\dash\data::page_title($myTitle);
		\dash\data::page_desc($myDesc);

		\dash\data::badge_text($myBadgeText);
		\dash\data::badge_link($myBadgeLink);

		\content_cms\posts\main\view::myDataType();
	}
}
?>