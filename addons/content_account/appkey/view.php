<?php
namespace content_account\appkey;

class view extends \content_account\main\view
{

	public function config()
	{
		$this->data->appkey = \dash\utility\appkey::get_app_key(\dash\user::id());
	}
}
?>