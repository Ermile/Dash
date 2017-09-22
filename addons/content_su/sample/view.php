<?php
namespace addons\content_su\sample;

class view extends \addons\content_su\main\view
{

	function config()
	{
		parent::config();

		switch (\lib\router::get_url(1))
		{
			case 'life':
				$this->data->bodyel = 'data-life=1';
				break;
		}
	}
}
?>