<?php
namespace addons\content_cp\posts\main;


class view extends \addons\content_cp\main\view
{
	public function config()
	{
		$this->data->moduleTypeTxt = \dash\request::get('type');
		$this->data->moduleType    = '';

		if(\dash\request::get('type'))
		{
			$this->data->moduleType = '?type='. \dash\request::get('type');
		}
	}
}
?>