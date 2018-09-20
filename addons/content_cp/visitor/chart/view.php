<?php
namespace content_cp\visitor\chart;

class view
{
	public static function config()
	{
		\dash\data::page_title("Visitors chart");
		\dash\data::page_pictogram('gauge');

		if(\dash\option::config('visitor'))
		{

			\dash\data::include_chart3(true);
		}
	}
}
?>