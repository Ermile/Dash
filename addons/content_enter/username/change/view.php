<?php
namespace addons\content_enter\username\change;

class view extends \addons\content_enter\main\view
{
	public function config()
	{
		parent::config();

		$this->data->get_username = \dash\user::login('username');

		$this->data->page['title']   = T_('Change username');
		$this->data->page['desc']    = $this->data->page['title'];
	}
}
?>