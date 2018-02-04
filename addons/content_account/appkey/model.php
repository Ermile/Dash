<?php
namespace content_account\appkey;


class model extends \content_account\main\model
{

	public function post_appkey()
	{
		if(!\lib\user::id())
		{
			return;
		}

		if(\lib\utility::post('add') === 'appkey')
		{
			$check = \lib\utility\appkey::create_app_key(\lib\user::id());
			if($check)
			{
				\lib\debug::true(T_("Creat new api key successfully complete"));
				$this->redirector($this->url('full'));
			}
			else
			{
				\lib\debug::error(T_("Error in create new api key"));
			}
		}
	}
}
?>