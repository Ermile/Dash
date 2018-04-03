<?php
namespace addons\content_cp\home;

class view extends \addons\content_cp\main\view
{
	public function config()
	{
		$this->data->bodyclass        = 'siftal';
		$this->include->css           = false;
		$this->include->js            = false;
		$this->global->js             = [];

		$this->data->display['cp_posts']  = "content_cp/posts/layout.html";
		$this->data->display['cpSample'] = "content_cp/sample/layout.html";


		$this->data->dash['version']    = \lib\engine\version::get();
		$this->data->dash['lastUpdate'] = \lib\utility\git::getLastUpdate();
		$this->data->dash['langlist']   = ['fa_IR' => 'Persian - فارسی',
											 'en_US' => 'English',
											 'ar_SU' => 'Arabic - العربية'];

		$this->data->page['title']       = T_(ucfirst( str_replace('/', ' ', \lib\url::directory()) ));

		$this->data->dir['right']     = $this->global->direction == 'rtl'? 'left':  'right';
		$this->data->dir['left']      = $this->global->direction == 'rtl'? 'right': 'left';
	}


	public function view_child()
	{
		$mytable                = $this->cpModule('table');
	}
}
?>