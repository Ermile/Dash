<?php
namespace addons\content_cp\home;

class view
{
	public static function config()
	{
		\lib\data::bodyclass('siftal');
		// $this->include->css           = false;
		// $this->include->js            = false;
		// $this->global->js             = [];

		\lib\data::display_cp_posts("content_cp/posts/layout.html");
		\lib\data::display_cpSample("content_cp/sample/layout.html");


		\lib\data::dash_version(\lib\engine\version::get());
		\lib\data::dash_lastUpdate(\lib\utility\git::getLastUpdate());

		// $this->data->page['title']       = T_(ucfirst( str_replace('/', ' ', \lib\url::directory()) ));

		// $this->data->dir['right']     = $this->global->direction == 'rtl'? 'left':  'right';
		// $this->data->dir['left']      = $this->global->direction == 'rtl'? 'right': 'left';
	}
}
?>