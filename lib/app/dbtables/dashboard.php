<?php
namespace dash\app\dbtables;

trait dashboard
{
	public static $life_time = 60 * 1;

	public static function dashboard($_clean_cache = false)
	{
		$result                        = [];
		$result['count_all']           = self::get_count_dbtables(['school_id' => \dash\school::id()], $_clean_cache);
		$result['count_enable']        = self::get_count_dbtables(['school_id' => \dash\school::id(), 'status' => 'enable'], $_clean_cache);
		$result['count_user_dbtables']    = self::count_chart_dbtables('user', $_clean_cache);
		$result['count_lesson_dbtables']  = self::count_chart_dbtables('lesson', $_clean_cache);
		$result['count_score_dbtables']   = self::count_chart_dbtables('score', $_clean_cache);
		$result['count_teacher_dbtables'] = self::count_chart_dbtables('teacher', $_clean_cache);

		return $result;
	}


	private static function get_count_dbtables($_where = null, $_clean_cache = false)
	{
		$key = "count_dbtables_". json_encode($_where). '_'. \dash\school::id();

		if($_clean_cache)
		{
			\dash\session::set($key, null);
			return null;
		}

		$result = \dash\session::get($key);
		if($result === null)
		{
			$result = \dash\dbtables\dbtabless::get_count($_where);
			$result = intval($result);
			\dash\session::set($key, $result, null, self::$life_time);
		}

		return $result;
	}


	private static function get_last_active($_clean_cache)
	{
		$key = "dbtables_last_acitve_list_". \dash\school::id();

		if($_clean_cache)
		{
			\dash\session::set($key, null);
			return null;
		}

		$result = \dash\session::get($key);
		if($result === null)
		{
			$result = \dash\dbtables\dbtabless::get_last_active(\dash\school::id(), 3);
			\dash\session::set($key, $result, null, self::$life_time);
		}

		return $result;
	}


	private static function count_chart_dbtables($_type = null, $_clean_cache = false)
	{
		$key = "count_chart_dbtables_". $_type. '_'. \dash\school::id();

		if($_clean_cache)
		{
			\dash\session::set($key, null);
			return null;
		}

		$result = \dash\session::get($key);
		if($result === null)
		{
			switch ($_type)
			{
				case 'user':
					$result = \dash\dbtables\dbtabless::chart_dbtables_user(\dash\school::id());
					break;

				case 'lesson':
					$result = \dash\dbtables\dbtabless::chart_dbtables_lesson(\dash\school::id());
					break;

				case 'score':
					$result = \dash\dbtables\dbtabless::chart_dbtables_score(\dash\school::id());
					break;

				case 'teacher':
					$result = \dash\dbtables\dbtabless::chart_dbtables_teacher(\dash\school::id());
					break;
			}
			$temp = [];
			if(is_array($result))
			{
				foreach ($result as $key => $value)
				{
					$temp[] = ['key' => $key, 'value' => $value];
				}
			}

			$temp = json_encode($temp, JSON_UNESCAPED_UNICODE);

			$result = $temp;

			\dash\session::set($key, $result, null, self::$life_time);
		}

		return $result;
	}

}
?>