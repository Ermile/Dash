<?php
namespace addons\content_enter\autoredirect;


class controller extends \addons\content_enter\main\controller
{
	public static $autoredirect_method = [];

	public function ready()
	{
		$autoredirect = [];

		$autoredirect['url']    = \lib\session::get('redirect_page_url');
		$autoredirect['method'] = \lib\session::get('redirect_page_method');
		$autoredirect['args']   = \lib\session::get('redirect_page_args');
		$autoredirect['title']  = \lib\session::get('redirect_page_title');
		$autoredirect['button'] = \lib\session::get('redirect_page_button');

		if(empty(array_filter($autoredirect)))
		{
			\lib\header::status(404);
		}
		else
		{
			self::$autoredirect_method = $autoredirect;
			$this->get(false, 'autoredirect')->ALL();
		}
	}
}
?>