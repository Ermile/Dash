<?php
namespace addons\content_enter\home;


class view
{

	public static function config()
	{
		\lib\data::mobileReadonly(false);

		\lib\data::page_special(true);
		\lib\data::page_title(T_('Enter to :name with mobile', ['name' => \lib\data::site_title()]));
		\lib\data::page_desc(\lib\data::page_title());

		$main_account = false;
		if(isset($_SESSION['main_account']))
		{
			$main_account = true;
		}

		$mobile = \lib\request::get('mobile');
		if($mobile)
		{
			if(!$main_account)
			{
				$mobile = \lib\utility\filter::mobile($mobile);
			}

			\lib\data::getMobile($mobile);
		}
	}
}
?>