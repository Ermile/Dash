<?php
namespace content_crm\member\add;


class view
{

	public static function config()
	{
		\dash\data::page_title(T_('Add new user'));
		\dash\data::page_desc(T_('You can add new member and after add with minimal data, we allow you to add extra detail of member.'));
		\dash\data::page_pictogram('user-plus');


		\dash\data::badge_text(T_('Back to list of users'));
		\dash\data::badge_link(\dash\url::this());

		\content_crm\member\main\view::static_var();
		\dash\data::display_donateMember('content_crm/member/layout.html');
	}
}
?>