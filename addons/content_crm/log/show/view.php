<?php
namespace content_crm\log\show;


class view
{
	public static function config()
	{
		$myTitle = T_("Log");
		$myDesc  = T_('Check list of log and search or filter in them to find your logs.');

		// add back level to summary link
		$product_list_link =  '<a href="'. \dash\url::this() .'" data-shortkey="121">'. T_('Back to dashboard'). '</a>';
		$myDesc .= ' | '. $product_list_link;

		\dash\data::page_title($myTitle);
		\dash\data::page_desc($myDesc);

		\dash\data::page_pictogram('pinboard');

		$log_id = \dash\request::get('id');

		if(!$log_id || !is_numeric($log_id))
		{
			\dash\header::status(404);
		}

		$dataRow = \dash\db\logs::get(['id' => $log_id, 'limit' => 1]);
		\dash\data::dataRow($dataRow);

	}
}
?>