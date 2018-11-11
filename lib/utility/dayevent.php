<?php
namespace dash\utility;


class dayevent
{
	public static function save()
	{
		$result = self::calc();
		if(!is_array($result))
		{
			return false;
		}

		$get_today = \dash\db\dayevent::get(['date' => date("Y-m-d"), 'limit' => 1]);

		if(!isset($get_today['id']))
		{
			$result['date']      = date("Y-m-d");
			$result['countcalc'] = 1;
			\dash\db\dayevent::insert($result);
		}
		else
		{
			if(isset($get_today['countcalc']))
			{
				$result['countcalc'] = intval($get_today['countcalc']) + 1;
			}
			else
			{
				$result['countcalc'] = 1;
			}

			\dash\db\dayevent::update($result, $get_today['id']);
		}
	}

	public static function calc()
	{
		$result = [];

		$result['dbtrafic']     = round(((intval(\dash\db::global_status(null, 'Bytes_sent')) + intval(\dash\db::global_status(null, 'Bytes_received'))) / 1024));
		$result['dbsize']       = \dash\db::get_size();
		$result['user']         = \dash\db\users::get_count();
		$result['activeuser']   = \dash\db\users::get_count(['status' => 'active']);
		$result['deactiveuser'] = \dash\db\users::get_count(['status' => ['<>', "'active'"]]);
		$result['log']          = \dash\db\logs::get_count();
		$result['visitor']      = \dash\db\visitors::get_count();
		$result['agent']        = \dash\db\agents::get_count();
		$result['session']      = \dash\db\sessions::get_count();
		$result['urls']         = \dash\db\visitors::url_get_count();
		$result['ticket']       = \dash\db\comments::get_count(['type' => 'ticket', 'parent' => null]);
		$result['comment']      = \dash\db\comments::get_count(['type' => ['<>', "'ticket'"]]);
		$result['address']      = \dash\db\address::get_count();;
		$result['news']         = \dash\db\posts::get_count(['type' => 'post']);
		$result['page']         = \dash\db\posts::get_count(['type' => 'page']);
		$result['post']         = \dash\db\posts::get_count(['type' => ['NOT IN ',"('post', 'page')"]]);
		$result['transaction']  = \dash\db\transactions::get_count();
		$result['term']         = \dash\db\terms::get_count();
		$result['termusages']   = \dash\db\termusages::get_count();

		if(is_callable(['\\lib\\dayevent', 'calc']))
		{
			$project_result = \lib\dayevent::calc();
			if(is_array($project_result))
			{
				$result = array_merge($result, $project_result);
			}
		}
		$result = array_map('intval', $result);
		return $result;
	}


	public static function chart()
	{
		$result = \dash\db\dayevent::get(['1.1' => 1.1]);

		$data       = [];
		$categories = [];

		foreach ($result as $record)
		{
			foreach ($record as $key => $value)
			{
				if(in_array($key, ['id','datecreated', 'datemodified']))
				{
					continue;
				}

				if($key === 'date')
				{
					array_push($categories, \dash\datetime::fit($value, null, 'date'));
					continue;
				}

				$temp = null;
				if($value)
				{
					$temp = floatval($value);
				}

				if(!isset($data[$key]))
				{
					$data[$key] = ['name' => $key, 'data' => []];
				}

				$data[$key]['data'][] = $temp;
			}
		}

		$data                 = array_values($data);
		$result               = [];
		$result['categories'] = json_encode($categories, JSON_UNESCAPED_UNICODE);
		$result['data']       = json_encode($data, JSON_UNESCAPED_UNICODE);

		return $result;
	}
}
?>