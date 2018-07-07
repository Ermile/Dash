<?php
namespace dash;


class log
{
	public static function db($_caller, $_args = [])
	{
		return \dash\db\logs::set($_caller, \dash\user::id(), $_args);
	}


	public static function before_after($_caller, $_before, $_after, $_args = [])
	{
		$log_detail           = [];
		$log_detail['before'] = [];
		$log_detail['after']  = [];
		$log_detail['vars']   = [];

		if(is_array($_args))
		{
			array_merge($log_detail, $_args);
		}

		if(is_array($_before) && is_array($_after))
		{
			foreach ($_before as $key => $value)
			{
				if(array_key_exists($key, $_after))
				{
					if($value != $_after[$key])
					{
						$log_detail['vars'][]   = $key;
						$log_detail['before'][] = $value;
						$log_detail['after'][]  = $_after[$key];
					}
				}
			}
		}

		if($log_detail['before'] && !empty($log_detail['before']))
		{
			$log_detail['before'] = implode(';', $log_detail['before']);
		}
		else
		{
			unset($log_detail['before']);
		}

		if($log_detail['after'] && !empty($log_detail['after']))
		{
			$log_detail['after'] = implode(';', $log_detail['after']);
		}
		else
		{
			unset($log_detail['after']);
		}

		if($log_detail['vars'] && !empty($log_detail['vars']))
		{
			$log_detail['vars'] = implode(';', $log_detail['vars']);
		}
		else
		{
			unset($log_detail['vars']);
		}

		return self::db($_caller, $log_detail);
	}
}
?>