<?php
namespace addons\content_su\tools\update;

class view extends \addons\content_su\main\view
{
	public function config()
	{
		parent::config();
		$this->data->page['title'] = T_("Update");
		$this->data->page['desc']  = T_('Check curent version of dash and update to latest version if available.');

		$this->data->dashLoc = null;;

		// go to root url
		if(is_dir(root. 'dash'))
		{
			$this->data->dashLoc = T_('Inside project'). ' <span class="sf-chain-broken fc-green"></span>';
		}
		elseif(is_dir(root. '../dash'))
		{
			$this->data->dashLoc = T_('Global'). ' <span class="sf-globe-1 fc-red"></span>';
		}
	}
}
?>