<?php
namespace addons\content_enter\autoredirect;


class view extends \addons\content_enter\main\view
{
	public function config()
	{
		parent::config();
		$this->data->page['title']   = T_('redirecting...');
		$this->data->page['desc']    = T_('in redirect process...');
		$this->data->page['special'] = true;
	}


	public function view_autoredirect()
	{
		$autoredirect = $this->controller()::$autoredirect_method;
		if(!empty($autoredirect))
		{
			$this->data->autoredirect = $autoredirect;
			\dash\session::set('redirect_page_url', null);
			\dash\session::set('redirect_page_method', null);
			\dash\session::set('redirect_page_args', null);
			\dash\session::set('redirect_page_title', null);
			\dash\session::set('redirect_page_button', null);
		}
	}
}
?>