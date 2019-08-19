<?php
namespace content_account\personalization\language;


class view
{

	public static function config()
	{
		\dash\data::page_title(T_('Language'));

		\dash\data::badge_link(\dash\url::this());
		\dash\data::badge_text(T_('Back to personal info'));



		$langList = \dash\language::all();

		\dash\data::langList($langList);

	}
}
?>
