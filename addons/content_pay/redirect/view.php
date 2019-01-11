<?php
namespace content_pay\redirect;


class view
{
	public static function config()
	{
		\dash\data::page_title(T_('redirecting...'));
		\dash\data::page_desc(T_('in redirect process...'));
		\dash\data::page_special(true);

		$autoredirect = \dash\temp::get('autoredirect');

		if(!empty($autoredirect))
		{
			\dash\data::autoredirect($autoredirect);

			\dash\session::set('redirect_page_url', null);
			\dash\session::set('redirect_page_method', null);
			\dash\session::set('redirect_page_args', null);
			\dash\session::set('redirect_page_title', null);
			\dash\session::set('redirect_page_button', null);
		}
	}
}
?>