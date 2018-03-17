<?php
namespace content_account\profile;

class view extends \content_account\main\view
{
	public function config()
	{
		$this->data->page['title'] = T_("Edit profile");
		$this->data->page['desc']  = T_("Check your profile and edit name or avatar of your account");
	}

	public function view_profile()
	{
		if(\lib\user::login('unit_id'))
		{
			$this->data->user_unit = \lib\utility\units::get(\lib\user::login('unit_id'), true);
		}
	}
}
?>