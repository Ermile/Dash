<?php
namespace dash\app;


class visitor
{
	private static function merge_args($_args)
	{
		$default_args =
		[
			'period'    => 'hours24',
			'type'      => null,
			'subdomain' => null,
		];

		if(!is_array($_args))
		{
			$_args = [];
		}

		return array_merge($default_args, $_args);
	}


	public static function total_visit($_args = [])
	{
		$_args = self::merge_args($_args);
		$total_visit = \dash\db\visitors::total_visit($_args);
		return intval($total_visit);
	}

	public static function total_visitor($_args = [])
	{
		$_args = self::merge_args($_args);
		$total_visitor = \dash\db\visitors::total_visitor($_args);
		return intval($total_visitor);
	}


	public static function total_avgtime($_args = [])
	{
		$_args = self::merge_args($_args);
		$total_avgtime = \dash\db\visitors::total_avgtime($_args);
		return intval($total_avgtime);
	}

	public static function total_maxtrafictime($_args = [])
	{
		$_args = self::merge_args($_args);
		$total_maxtrafictime = \dash\db\visitors::total_maxtrafictime($_args);
		return $total_maxtrafictime;
	}

	public static function chart_visitorchart($_args = [])
	{
		$_args = self::merge_args($_args);
		$chart_visitorchart = \dash\db\visitors::chart_visitorchart($_args);

		if(isset($chart_visitorchart['visitor']) && isset($chart_visitorchart['visit']) && is_array($chart_visitorchart['visitor']) && is_array($chart_visitorchart['visit']))
		{
			$chart = [];
			$hi_chart = [];
			foreach ($chart_visitorchart['visit'] as $key => $value)
			{
				if(isset($chart_visitorchart['visitor'][$key]))
				{
					$date = \dash\datetime::fit($key, true, 'date');
					if($_args['period'] === 'hours24')
					{
						$date = \dash\utility\human::fitNumber($key);
					}

					$chart[] =
					[
						'date'    => $date,
						'visit'   => intval($value),
						'visitor' => intval($chart_visitorchart['visitor'][$key])
					];
				}
			}

			$hi_chart['categories'] = json_encode(array_column($chart, 'date'), JSON_UNESCAPED_UNICODE);
			$hi_chart['visit']      = json_encode(array_column($chart, 'visit'), JSON_UNESCAPED_UNICODE);
			$hi_chart['visitor']    = json_encode(array_column($chart, 'visitor'), JSON_UNESCAPED_UNICODE);
			return $hi_chart;
			// return json_encode($chart, JSON_UNESCAPED_UNICODE);

		}
		return null;
	}


	public static function chart($_args)
	{
		$_args = self::merge_args($_args);

		$data = [];
		$new_data = [];

		switch ($_args['type'])
		{
			case 'device':
				// not ready yet!
				break;
			case 'country':
				$data = \dash\db\visitors::chart_country($_args);
				$new_data = [];
				foreach ($data as $key => $value)
				{
					if(!$key)
					{
						$key = T_("Unknown");
					}
					$new_data[] = ["id" => mb_strtoupper($key), "value" => $value];
				}
				return json_encode($new_data, JSON_UNESCAPED_UNICODE);
				break;

			case 'browser':
				$data = \dash\db\visitors::chart_browser($_args);
				break;

			case 'os':
				$data = \dash\db\visitors::chart_os($_args);
				break;

			default:
				return null;
				break;
		}
		if(!is_array($data))
		{
			$data = [];
		}

		$new_data = [];
		foreach ($data as $key => $value)
		{
			if(!$key)
			{
				$key = T_("Unknown");
			}
			$new_data[] = ["key" => $key, "value" => $value];
		}
		return json_encode($new_data, JSON_UNESCAPED_UNICODE);

	}
}
?>