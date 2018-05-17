<?php
namespace content_cp\home;

class view
{
	public static function config()
	{
		\dash\data::display_cp_posts("content_cp/posts/layout.html");
		\dash\data::display_cpSample("content_cp/sample/layout.html");

		\dash\data::dash_version(\dash\engine\version::get());
		\dash\data::dash_lastUpdate(\dash\utility\git::getLastUpdate());

		if(\dash\option::config('visitor'))
		{
			\dash\data::visitor_chart(\dash\utility\visitor::chart(true));
			\dash\data::visitor_totalpages(\dash\utility\visitor::top_pages());

			\dash\data::include_chart(true);
		}
		// $this->data->page['title']       = T_(ucfirst( str_replace('/', ' ', \dash\url::directory()) ));

		// $this->data->dir['right']     = $this->global->direction == 'rtl'? 'left':  'right';
		// $this->data->dir['left']      = $this->global->direction == 'rtl'? 'right': 'left';
	}
}
?>