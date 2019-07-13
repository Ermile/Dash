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
		$result                    = [];

		$result['dbtrafic']        = round(((intval(\dash\db::global_status(null, 'Bytes_sent')) + intval(\dash\db::global_status(null, 'Bytes_received'))) / 1024));
		$result['dbsize']          = \dash\db::get_size();
		$result['user']            = \dash\db\users::get_count();
		$result['activeuser']      = \dash\db\users::get_count(['status' => 'active']);
		$result['deactiveuser']    = \dash\db\users::get_count(['status' => 'deactive']);
		$result['user_awaiting']   = \dash\db\users::get_count(['status' => 'awaiting']);
		$result['user_removed']    = \dash\db\users::get_count(['status' => 'removed']);
		$result['user_filter']     = \dash\db\users::get_count(['status' => 'filter']);
		$result['user_unreachabl'] = \dash\db\users::get_count(['status' => 'unreachabl']);

		$result['log']             = \dash\db\logs::get_count();
		$result['visitor']         = \dash\db\visitors::get_count();
		$result['agent']           = \dash\db\agents::get_count();
		$result['session']         = \dash\db\sessions::get_count();
		$result['urls']            = \dash\db\visitors::url_get_count();
		$result['ticket']          = \dash\db\comments::get_count(['type' => 'ticket', 'parent' => null]);
		$result['ticket_message']          = \dash\db\comments::get_count(['type' => 'ticket', 'parent' => ['IS NOT', 'NULL']]);
		$result['comment']         = \dash\db\comments::get_count(['type' => ['<>', "'ticket'"]]);
		$result['address']         = \dash\db\address::get_count();

		$result['news']            = \dash\db\posts::get_count(['type' => 'post']);
		$result['page']            = \dash\db\posts::get_count(['type' => 'page']);
		$result['help']            = \dash\db\posts::get_count(['type' => 'help']);
		$result['attachment']      = \dash\db\posts::get_count(['type' => 'attachment']);
		$result['post']            = \dash\db\posts::get_count(['type' => ['NOT IN ',"('post', 'page', 'help', 'attachment')"]]);

		$result['transaction']     = \dash\db\transactions::get_count();

		$result['term']            = \dash\db\terms::get_count();
		$result['tag']             = \dash\db\terms::get_count(['type' => 'tag']);
		$result['cat']             = \dash\db\terms::get_count(['type' => 'cat']);
		$result['support_tag']     = \dash\db\terms::get_count(['type' => 'support_tag']);
		$result['help_tag']        = \dash\db\terms::get_count(['type' => 'help_tag']);

		$result['termusages']      = \dash\db\termusages::get_count();

		$result['user_mobile']     = \dash\db\users::get_count(['mobile' => ['IS NOT', 'NULL']]);
		$result['user_email']      = \dash\db\users::get_count(['email' => ['IS NOT', 'NULL']]);
		$result['user_username']   = \dash\db\users::get_count(['username' => ['IS NOT', 'NULL']]);
		$result['user_chatid']     = \dash\db\user_telegram::get_count();
		$result['user_android']    = \dash\db\user_android::get_count();

		$result['user_permission'] = \dash\db\users::get_count(['permission' => ['IS NOT', 'NULL']]);

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


	public static function chart($_option = [])
	{
		$fields = [];
		if(isset($_option['field']) && is_array($_option['field']))
		{
			$fields = $_option['field'];
		}

		$result = \dash\db\dayevent::get_all();

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

				if($fields && !in_array($key, $fields))
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
					$data[$key] = ['name' => T_($key), 'data' => []];
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



	public static function day_notif()
	{
		$today         = date("Y-m-d", strtotime("-1 days"));
		$yesterday     = date("Y-m-d", strtotime("-2 days"));

		$get_today     = \dash\db\dayevent::get(['date' => $today, 'limit' => 1]);
		$get_yesterday = \dash\db\dayevent::get(['date' => $yesterday, 'limit' => 1]);

		if(!is_array($get_today) || !is_array($get_yesterday))
		{
			return false;
		}

		$diff    = [];
		$sum_diff = 0;
		foreach ($get_today as $key => $value)
		{
			if(in_array($key, ['dbtrafic', 'dbsize', 'id', 'date', 'countcalc']))
			{
				continue;
			}

			$temp_diff = 0;
			if(array_key_exists($key, $get_yesterday))
			{
				$temp_diff = intval($value) - intval($get_yesterday[$key]);
			}

			if(!$temp_diff || !$value)
			{
				continue;
			}

			$sum_diff += $temp_diff;
			$diff[$key] = ["now" => intval($value), "daydiff" => $temp_diff];
		}

		$diff['sum_diff'] = $sum_diff;

		\dash\log::set('su_dayEventReport', ['result' => $diff]);
	}
}
?>