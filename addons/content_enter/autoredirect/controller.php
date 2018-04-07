<?php
namespace content_enter\autoredirect;


class controller extends \addons\content_enter\main\controller
{
	public static $autoredirect_method = [];

	public function ready()
	{
		$autoredirect = [];

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
			self::$autoredirect_method = $autoredirect;
			$this->get(false, 'autoredirect')->ALL();
		}
	}
}
?>