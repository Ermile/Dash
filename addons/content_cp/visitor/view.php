<?php
namespace content_cp\visitor;

class view
{
	public static function config()
	{
		\dash\data::page_title("Visitors chart");
		\dash\data::page_pictogram('gauge');

		if(\dash\option::config('visitor'))
		{

			\dash\data::visitor_chart(\dash\utility\visitor::chart(true));
			\dash\data::visitor_totalpages(\dash\utility\visitor::top_pages());

			\dash\data::include_chart3(true);
		}
	}
}
?>