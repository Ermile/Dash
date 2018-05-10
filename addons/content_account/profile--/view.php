<?php
namespace content_account\profile;

class view
{
	public static function config()
	{
		\dash\data::page_title(T_("Edit profile"));
		\dash\data::page_desc(T_("Check your profile and edit name or avatar of your account"));
		if(\dash\user::login('unit_id'))
		{
			\dash\data::userUnit(\dash\app\units::get(\dash\user::login('unit_id'), true));
		}
	}
}
?>