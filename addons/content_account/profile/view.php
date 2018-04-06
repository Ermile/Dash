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
		if(\dash\user::login('unit_id'))
		{
			$this->data->user_unit = \dash\app\units::get(\dash\user::login('unit_id'), true);
		}
	}
}
?>