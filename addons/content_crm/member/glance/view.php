<?php
namespace content_crm\member\glance;


class view
{
	public static function config()
	{
		\content_crm\member\main\view::dataRowMember();

		\dash\data::page_title(T_('Glance user'));
		\dash\data::page_desc(' ');
		\dash\data::page_pictogram('user');

		if(\dash\permission::supervisor() && \dash\request::get('showlog'))
		{
			$user_code = \dash\data::dataRowMember_id();
			$user_id   = \dash\coding::decode($user_code);

			\dash\data::showUserLog(\dash\app\user::user_in_all_table($user_id));
		}
	}
}
?>