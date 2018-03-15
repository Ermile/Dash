<?php
namespace addons\content_cp\posts\main;


class view extends \addons\content_cp\main\view
{
	public function config()
	{
		$this->data->moduleTypeTxt = \lib\request::get('type');
		$this->data->moduleType    = '';

		if(\lib\request::get('type'))
		{
			$this->data->moduleType = '?type='. \lib\request::get('type');
		}
	}
}
?>