<?php
namespace content_account\profile;

class view extends \content_account\main\view
{
	public function view_profile()
	{
		if($this->login('unit_id'))
		{
			$this->data->user_unit = \lib\utility\units::get($this->login('unit_id'), true);
		}
	}
}
?>