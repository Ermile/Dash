<?php
namespace content_enter\home;


class view
{

	public static function config()
	{
		\dash\data::page_special(true);
		\dash\data::page_title(T_('Enter to :name', ['name' => \dash\data::site_title()]));
		\dash\data::page_desc(\dash\data::site_desc());

		if(mb_strlen(\dash\data::page_desc()) < 150)
		{
			\dash\data::page_desc(\dash\data::page_desc(). ' | '. \dash\data::site_title());
			if(mb_strlen(\dash\data::page_desc()) < 150)
			{
				\dash\data::page_desc(\dash\data::page_desc(). ' | '. \dash\data::service_title());
			}
		}
		\dash\data::mobileReadonly(false);

		$main_account = false;
		if(isset($_SESSION['main_account']) && $_SESSION['main_account'])
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

		if(\dash\request::get('autosend') && \dash\request::get('mobile'))
		{
			$mobile = \dash\utility\filter::mobile(\dash\request::get('mobile'));
			if($mobile)
			{
				\content_enter\home\model::enter_post($mobile);
			}
		}
	}
}
?>