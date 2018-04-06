<?php
namespace content_enter\home;


class view
{

	public static function config()
	{
		\dash\data::page_special(true);
		\dash\data::page_title(T_('Enter to :name with mobile', ['name' => \dash\data::site_title()]));
		\dash\data::page_desc(\dash\data::page_title());
		\dash\data::mobileReadonly(false);

		$main_account = false;
		if(isset($_SESSION['main_account']))
		{
			$main_account = true;
		}

		$mobile = \dash\request::get('mobile');
		if($mobile)
		{
			if(!$main_account)
			{
				$mobile = \dash\utility\filter::mobile($mobile);
			}

			\dash\data::getMobile($mobile);
		}
	}
}
?>