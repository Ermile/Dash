<?php
namespace dash\app\dbtables;

trait dashboard
{
	public static $life_time = 60 * 1;

	public static function dashboard($_clean_cache = false)
	{
		$result                        = [];
		$result['count_all']           = self::get_count_dbtables(['school_id' => \lib\school::id()], $_clean_cache);
		$result['count_enable']        = self::get_count_dbtables(['school_id' => \lib\school::id(), 'status' => 'enable'], $_clean_cache);
		$result['count_user_dbtables']    = self::count_chart_dbtables('user', $_clean_cache);
		$result['count_lesson_dbtables']  = self::count_chart_dbtables('lesson', $_clean_cache);
		$result['count_score_dbtables']   = self::count_chart_dbtables('score', $_clean_cache);
		$result['count_teacher_dbtables'] = self::count_chart_dbtables('teacher', $_clean_cache);

		return $result;
	}


	private static function get_count_dbtables($_where = null, $_clean_cache = false)
	{
		$key = "count_dbtables_". json_encode($_where). '_'. \lib\school::id();

		if($_clean_cache)
		{
			\lib\session::set($key, null);
			return null;
		}

		$result = \lib\session::get($key);
		if($result === null)
		{
			$result = \lib\dbtables\dbtabless::get_count($_where);
			$result = intval($result);
			\lib\session::set($key, $result, null, self::$life_time);
		}

		return $result;
	}


	private static function get_last_active($_clean_cache)
	{
		$key = "dbtables_last_acitve_list_". \lib\school::id();

		if($_clean_cache)
		{
			\lib\session::set($key, null);
			return null;
		}

		$result = \lib\session::get($key);
		if($result === null)
		{
			$result = \lib\dbtables\dbtabless::get_last_active(\lib\school::id(), 3);
			\lib\session::set($key, $result, null, self::$life_time);
		}

		return $result;
	}


	private static function count_chart_dbtables($_type = null, $_clean_cache = false)
	{
		$key = "count_chart_dbtables_". $_type. '_'. \lib\school::id();

		if($_clean_cache)
		{
			\lib\session::set($key, null);
			return null;
		}

		$result = \lib\session::get($key);
		if($result === null)
		{
			switch ($_type)
			{
				case 'user':
					$result = \lib\dbtables\dbtabless::chart_dbtables_user(\lib\school::id());
					break;

				case 'lesson':
					$result = \lib\dbtables\dbtabless::chart_dbtables_lesson(\lib\school::id());
					break;

				case 'score':
					$result = \lib\dbtables\dbtabless::chart_dbtables_score(\lib\school::id());
					break;

				case 'teacher':
					$result = \lib\dbtables\dbtabless::chart_dbtables_teacher(\lib\school::id());
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

			\lib\session::set($key, $result, null, self::$life_time);
		}

		return $result;
	}

}
?>