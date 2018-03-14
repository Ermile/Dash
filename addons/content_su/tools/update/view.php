<?php
namespace addons\content_su\tools\update;

class view extends \addons\content_su\main\view
{
	public function config()
	{
		parent::config();
		$this->data->page['title'] = T_("Update");
		$this->data->page['desc']  = T_('Check curent version of dash and update to latest version if available.');
	}
}
?>