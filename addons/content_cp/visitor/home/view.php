<?php
namespace content_cp\visitor\home;


class view
{
	public static function config()
	{
		$myTitle = T_("Visitor");
		$myDesc  = T_('Check list of visitor and search or filter in them to find your visitor.');

		// add back level to summary link
		$product_list_link =  '<a href="'. \dash\url::here() .'" data-shortkey="121">'. T_('Back to dashboard'). '</a>';
		$myDesc .= ' | '. $product_list_link;

		\dash\data::page_title($myTitle);
		\dash\data::page_desc($myDesc);

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

		if(\dash\request::get('url'))
		{
			$args['urls.url'] = $_GET['url'];
		}

		if(\dash\request::get('domain'))
		{
			$args['urls.domain'] = $_GET['domain'];
		}

		if(\dash\request::get('query'))
		{
			$args['urls.query'] = $_GET['query'];
		}

		if(\dash\request::get('service_id'))
		{
			$args['visitors.service_id'] = $_GET['service_id'];
		}

		if(\dash\request::get('visitor_ip'))
		{
			$args['visitors.visitor_ip'] = $_GET['visitor_ip'];
		}

		if(\dash\request::get('external'))
		{
			$args['visitors.external'] = $_GET['external'];
		}

		if(\dash\request::get('date'))
		{
			$args['visitors.date'] = $_GET['date'];
		}

		if(\dash\request::get('ref_url'))
		{
			$args['referer.url'] = $_GET['ref_url'];
		}

		if(\dash\request::get('ref_pwd'))
		{
			$args['referer.pwd'] = $_GET['ref_pwd'];
		}

		if(\dash\request::get('userid'))
		{
			$user_id = \dash\coding::decode(\dash\request::get('userid'));
			if($user_id)
			{
				$args['visitors.user_id'] = $user_id;
			}
		}


		if(\dash\request::get('type') && in_array(\dash\request::get('type'), ['before', 'after']))
		{
			if(\dash\request::get('datetime'))
			{
				$datetime = \dash\request::get('datetime');
				if(strtotime($datetime) !== false)
				{
					$operation = \dash\request::get('type') === 'after' ? ' > ' : ' < ';
					$args['visitors.timeraw'] = [$operation, (string) strtotime($datetime)];
				}
			}
		}


		$dataTable = \dash\db\visitors::search(\dash\request::get('q'), $args);

		$dataTable = array_map(['self', 'ready'], $dataTable);

		\dash\data::dataTable($dataTable);

		$filterArray = $args;

		// 'id'            => string '10350' (length=5)
		// 'url'           => string '/cp/log' (length=7)
		// 'host'          => string 'azvir.local' (length=11)
		// 'domain'        => string 'http://haram2.azvir.local' (length=25)
		// 'query'         => string 'q=ByRemem' (length=9)
		// 'urlmd5'        => string '2b7453f9a4dc7dbbf865af77e772fe1c' (length=32)
		// 'pwd'           => string 'http://haram2.azvir.local/cp/log?q=ByRemem' (length=42)
		// 'service_id'    => string '3' (length=1)
		// 'visitor_ip'    => string '2130706433' (length=10)
		// 'url_id'        => string '605' (length=3)
		// 'url_idreferer' => string '604' (length=3)
		// 'agent_id'      => string '1' (length=1)
		// 'user_id'       => string '2' (length=1)
		// 'user_idteam'   => null
		// 'external'      => string '1' (length=1)
		// 'date'          => string '2018-07-23' (length=10)
		// 'time'          => string '19:29:25' (length=8)
		// 'timeraw'       => string '1532357965' (length=10)
		// 'year'          => string '2018' (length=4)
		// 'month'         => string '7' (length=1)
		// 'day'           => string '23' (length=2)
		// 'datemodified'  => null
		// 'ref_url'       => string '/cp/log' (length=7)
		// 'ref_pwd'       => string 'http://haram2.azvir.local/cp/log?q=mem' (length=38)
		// 'ref_host'      => string 'azvir.local' (length=11)
		// 'ref_domain'    => string 'http://haram2.azvir.local' (length=25)

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