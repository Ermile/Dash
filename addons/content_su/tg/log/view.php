<?php
namespace content_su\tg\log;


class view
{
	public static function config()
	{
		$myTitle = T_("Telegram log");
		$myDesc  = T_('Check list of telegram and search or filter in them to find your telegram.');
		\dash\data::page_title($myTitle);
		\dash\data::page_desc($myDesc);
		\dash\data::page_pictogram('briefcase');
		\dash\data::badge_text(T_('Back to dashboard'));
		\dash\data::badge_link(\dash\url::this());



		$search_string = \dash\request::get('q');
		if($search_string)
		{
			$myTitle .= ' | '. T_('Search for :search', ['search' => $search_string]);
		}

		$allow = ['user','mobile','userid','user_id','hooktext','hookdate','step','sendmethod','sendtext','senddate','responsedate','status',];

		$args = \dash\request::get();

		if(is_array($args))
		{
			foreach ($args as $key => $value)
			{
				if(!in_array($key, $allow))
				{
					unset($args[$key]);
				}
				else
				{
					$args['telegrams.'. $key] = $value;
					unset($args[$key]);
				}
			}
		}


		$args['sort']  = \dash\request::get('sort');
		$args['order'] = \dash\request::get('order');

		if(!$args['order'])
		{
			$args['order'] = 'DESC';
		}

		if(!$args['sort'])
		{
			$args['sort'] = 'telegrams.id';
		}

		$args['join_user'] = true;

		unset($args['page']);

		$dataTable = \dash\db\telegrams::search(\dash\request::get('q'), $args);

		if(is_array($dataTable))
		{
			$dataTable = array_map(['\dash\app', 'fix_avatar'], $dataTable);
		}

		\dash\data::dataTable($dataTable);

		$filterArray = $args;
		$dataFilter = \dash\app\sort::createFilterMsg($search_string, $filterArray);
		\dash\data::dataFilter($dataFilter);
	}
}
?>