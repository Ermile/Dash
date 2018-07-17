<?php
namespace content_enter\autoredirect;


class controller
{
	public static function routing()
	{
		$autoredirect           = [];

		$autoredirect['url']    = \dash\session::get('redirect_page_url');
		$autoredirect['method'] = \dash\session::get('redirect_page_method');
		$autoredirect['args']   = \dash\session::get('redirect_page_args');
		$autoredirect['title']  = \dash\session::get('redirect_page_title');
		$autoredirect['button'] = \dash\session::get('redirect_page_button');

		if(empty(array_filter($autoredirect)))
		{
			\dash\header::status(404);
		}
		else
		{
			\dash\log::db('autoRedirect', ['data' => $autoredirect['url']]);
			\dash\temp::set('autoredirect', $autoredirect);
		}
	}
}
?>