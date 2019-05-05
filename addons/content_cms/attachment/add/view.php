<?php
namespace content_cms\attachment\add;

class view
{
	public static function config()
	{
		\dash\permission::access('cpPostsAdd');

		$moduleTypeTxt = 'attachemnt';
		$moduleType    = 'attachemnt';

		\dash\data::page_pictogram('file-text-o');

		\dash\data::moduleTypeTxt($moduleTypeTxt);
		\dash\data::moduleType($moduleType);


		$myTitle   = T_("Add new attachemnt");
		$myDesc    = ' ';

		$myBadgeLink = \dash\url::this();
		$myBadgeText = T_('Back to list of attachemnt');


		\dash\data::page_title($myTitle);
		\dash\data::page_desc($myDesc);

		\dash\data::badge_text($myBadgeText);
		\dash\data::badge_link($myBadgeLink);

		\content_cms\posts\main\view::myDataType();
	}
}
?>