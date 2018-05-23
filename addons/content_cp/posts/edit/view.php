<?php
namespace content_cp\posts\edit;

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

		$id = \dash\request::get('id');

		$detail = \dash\app\posts::get($id);
		if(!$detail)
		{
			\dash\header::status(403, T_("Invalid id"));
		}

		\dash\data::dataRow($detail);
		\dash\data::listCats(\dash\app\term::cat_list());

		$myTitle = T_("Edit post");
		$myDesc  = T_("You can change everything, change url and add gallery or some other change");

		$myBadgeLink = \dash\url::this(). $moduleType;
		$myBadgeText = T_('Back to list of posts');

		$myType = \dash\request::get('type');

		if($myType)
		{
			switch ($myType)
			{
				case 'page':
					\dash\permission::access('cpPageEdit');
					$myTitle = T_('Edit page');
					$myBadgeText = T_('Back to list of pages');
					break;

				case 'post':
				default:
					\dash\permission::access('cpPostsEdit');
					$myTitle = T_('Edit post');
					$myBadgeText = T_('Back to list of posts');
					break;
			}
		}
		else
		{
			\dash\permission::access('cpPostsEdit');
		}

		\dash\data::page_title($myTitle);
		\dash\data::page_desc($myDesc);

		\dash\data::badge_text($myBadgeText);
		\dash\data::badge_link($myBadgeLink);
	}
}
?>
