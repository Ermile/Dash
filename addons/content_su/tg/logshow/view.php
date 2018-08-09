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
		if(is_array($load))
		{
			$new = [];
			foreach ($load as $key => $value)
			{
				if(is_string($value))
				{
					if(substr($value, 0, 1) === '{' || substr($value, 0, 1) === '[')
					{
						$load[$key] = json_decode($value, true);
					}
				}
			}
		}
		\dash\data::dataRow($load);

	}
}
?>