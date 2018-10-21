<?php
namespace content_su\tg\logshow;


class view
{
	public static function config()
	{
		$myTitle = T_("Telegram log");
		$myDesc  = T_('Check list of telegram and search or filter in them to find your telegram.');

		\dash\data::page_title($myTitle);
		\dash\data::page_desc($myDesc);
		// add back level to summary link
		\dash\data::badge_text(T_('Back to log list'));
		\dash\data::badge_link(\dash\url::this() .'/log');


		$load = \dash\db\telegrams::get(['id' => \dash\request::get('id'), 'limit' => 1]);
		// if(is_array($load))
		// {
		// 	$new = [];
		// 	foreach ($load as $key => $value)
		// 	{
		// 		var_dump($key);
		// 		var_dump($value);

		// 		if(is_string($value))
		// 		{
		// 			var_dump(111);
		// 			if(substr($value, 0, 1) === '{' || substr($value, 0, 1) === '[')
		// 			{
		// 				$tmp = json_decode($value, true);
		// 				$load[$key] = \dash\social\telegram::json($tmp);
		// 			}
		// 		}
		// 	}
		// }
		\dash\data::dataRow($load);

		// \dash\db\telegrams::get(['key' => 'value', 'key2' => 'value2']);
		// \dash\db\telegrams::get(['key' => 'value', 'key2' => 'value2', 'limit' => 1]); // return array by size 1
		// \dash\db\telegrams::insert(['key' => 'value', 'key2' => 'value2']);
		// \dash\db\telegrams::update(['key' => 'value', 'key2' => 'value2'], 10); // where id = 10 update
	}
}
?>