<?php
namespace content_account\my\avatar;


class view
{

	public static function config()
	{
		\dash\data::page_title(T_('Edit your avatar'));
		\dash\data::page_desc(T_('You can edit your avatar.'));


		\dash\data::badge_link(\dash\url::this());
		\dash\data::badge_text(T_('Back to personal info'));

		\content_account\my\view::load_me();
	}
}
?>
