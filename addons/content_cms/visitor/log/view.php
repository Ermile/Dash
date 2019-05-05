<?php
namespace content_cms\visitor\log;


class view
{
	public static function config()
	{
		$myTitle = T_("Visitor");
		$myDesc  = T_('Check list of visitor and search or filter in them to find your visitor.');

		\dash\data::page_title($myTitle);
		\dash\data::page_desc($myDesc);

		\dash\data::badge_link(\dash\url::this());
		\dash\data::badge_text(T_("Dashboard"));

		\dash\data::page_pictogram('pinboard');

		$search_string = \dash\request::get('q');
		if($search_string)
		{
			$myTitle .= ' | '. T_('Search for :search', ['search' => $search_string]);
		}

		$args =
		[
			'sort'  => \dash\request::get('sort'),
			'order' => \dash\request::get('order'),
		];

		if(!$args['order'])
		{
			$args['order'] = 'DESC';
		}

		if(!$args['sort'])
		{
			$args['sort'] = 'visitors.id';
		}

		if(\dash\request::get('id'))
		{
			$args['visitors.id'] = \dash\request::get('id');
		}
		if(\dash\request::get('user'))
		{
			$userid = \dash\coding::decode(\dash\request::get('user'));
			if($userid)
			{
				$args['visitors.user_id'] = $userid;
			}
		}
		if(\dash\request::get('userid'))
		{
			$userid = \dash\coding::decode(\dash\request::get('userid'));
			if($userid)
			{
				$args['visitors.user_id'] = $userid;
			}
		}
		if(\dash\request::get('user_id'))
		{
			$userid = \dash\coding::decode(\dash\request::get('user_id'));
			if($userid)
			{
				$args['visitors.user_id'] = $userid;
			}
		}
		if(\dash\request::get('session_id'))
		{
			$args['visitors.session_id'] = \dash\request::get('session_id');
		}
		if(\dash\request::get('date'))
		{
			$date = \dash\request::get('date');
			$args['1.1'] = [" = 1.1 ", " AND DATE(visitors.date) = DATE('$date')"];
		}
		if(\dash\request::get('time'))
		{
			$time = \dash\request::get('time');
			$args['1.1'] = [" = 1.1 ", " AND TIME(visitors.date) = TIME('$time')"];
		}
		if(\dash\request::get('datetime'))
		{
			$args['visitors.date'] = \dash\request::get('datetime');
		}

		if(\dash\request::get('avgtime'))
		{
			$args['visitors.avgtime'] = \dash\request::get('avgtime');
		}
		if(\dash\request::get('group'))
		{
			$args['agents.group'] = \dash\request::get('group');
		}
		if(\dash\request::get('name'))
		{
			$args['agents.name'] = \dash\request::get('name');
		}
		if(\dash\request::get('version'))
		{
			$args['agents.version'] = \dash\request::get('version');
		}
		if(\dash\request::get('os'))
		{
			$args['agents.os'] = \dash\request::get('os');
		}
		if(\dash\request::get('statuscode'))
		{
			$args['visitors.statuscode'] = \dash\request::get('statuscode');
		}
		if(\dash\request::get('method'))
		{
			$args['visitors.method'] = \dash\request::get('method');
		}
		if(\dash\request::get('country'))
		{
			$args['visitors.country'] = \dash\request::get('country');
		}
		if(\dash\request::get('visitor_ip'))
		{
			$args['visitors.visitor_ip'] = ip2long(\dash\request::get('visitor_ip'));
		}
		if(\dash\request::get('subdomain'))
		{
			$args['urls.subdomain'] = \dash\request::get('subdomain');
		}
		if(\dash\request::get('domain'))
		{
			$args['urls.domain'] = \dash\request::get('domain');
		}
		if(\dash\request::get('pwd'))
		{
			$args['urls.pwd'] = \dash\request::get('pwd');
		}
		if(\dash\request::get('ref_pwd'))
		{
			$args['referer.pwd'] = \dash\request::get('ref_pwd');
		}

		if(\dash\request::get('type') && in_array(\dash\request::get('type'), ['before', 'after']))
		{
			if(\dash\request::get('datetime'))
			{
				$datetime = \dash\request::get('datetime');
				if(strtotime($datetime) !== false)
				{
					$datetime = \dash\request::get('datetime');
					$operation = \dash\request::get('type') === 'after' ? ' > ' : ' < ';
					$args['visitors.date'] = [$operation, "$datetime"];
				}
			}
		}


		$dataTable = \dash\db\visitors::search(\dash\request::get('q'), $args);

		$dataTable = array_map(['self', 'ready'], $dataTable);

		\dash\data::dataTable($dataTable);


		$filterArray = $args;
		// set dataFilter
		$dataFilter = \dash\app\sort::createFilterMsg($search_string, $filterArray);
		\dash\data::dataFilter($dataFilter);
	}

	public static function ready($_data)
	{
		$result = [];
		foreach ($_data as $key => $value)
		{
			switch ($key)
			{
				case 'visitor_ip':
					$result[$key] = long2ip($value);
					break;

				case 'user_id':
					if(isset($value))
					{
						$result[$key] = \dash\coding::encode($value);
					}
					else
					{
						$result[$key] = null;
					}
					break;

				default:
					$result[$key] = $value;
					break;
			}
		}

		return $result;
	}
}
?>