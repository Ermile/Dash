<?php
namespace addons\content_cp\posts\main;


class view extends \addons\content_cp\main\view
{
	public function config()
	{
		$this->data->modulePath    = \lib\url::here(). '/posts';
		$this->data->moduleTypeTxt = \lib\utility::get('type');
		$this->data->moduleType    = '';

		if(\lib\utility::get('type'))
		{
			$this->data->moduleType = '?type='. \lib\utility::get('type');
		}
	}
}
?>