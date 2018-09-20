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

		if(\dash\request::get('visitor_ip'))
		{
			$args['visitors.visitor_ip'] = $_GET['visitor_ip'];
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
					$datetime = \dash\request::get('datetime');
					$operation = \dash\request::get('type') === 'after' ? ' > ' : ' < ';
					$args['visitors.date'] = [$operation, "$datetime"];
				}
			}
		}


		$dataTable = \dash\db\visitors::search(\dash\request::get('q'), $args);

		$dataTable = array_map(['self', 'ready'], $dataTable);

		\dash\data::dataTable($dataTable);

		// 'id'            => string '110' (length=3)
		// 'urlmd5'        => string '8cabfbd3156e965bb78885ef93d79ccd' (length=32)
		// 'domain'        => string 'azvir.local' (length=11)
		// 'subdomain'     => null
		// 'path'          => string '/cp/visitor' (length=11)
		// 'query'         => null
		// 'pwd'           => string 'http://azvir.local/cp/visitor' (length=29)
		// 'datecreated'   => string '2018-09-19 22:42:29' (length=19)
		// 'statuscode'    => string '200' (length=3)
		// 'visitor_ip'    => string '2130706433' (length=10)
		// 'session_id'    => string 'qng0t7eumeft6h554b1rm4d4hj' (length=26)
		// 'url_id'        => string '17' (length=2)
		// 'url_idreferer' => string '16' (length=2)
		// 'agent_id'      => string '1' (length=1)
		// 'user_id'       => string '2' (length=1)
		// 'date'          => string '2018-09-20 12:45:18' (length=19)
		// 'avgtime'       => null
		// 'ref_url'       => string '/cp' (length=3)
		// 'ref_pwd'       => string 'http://azvir.local/cp' (length=21)
		// 'ref_domain'    => string 'azvir.local' (length=11)


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