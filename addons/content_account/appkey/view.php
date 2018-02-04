<?php
namespace content_account\appkey;

class view extends \content_account\main\view
{

	public function config()
	{
		$this->data->appkey = \lib\utility\appkey::get_app_key(\lib\user::id());
	}
}
?>