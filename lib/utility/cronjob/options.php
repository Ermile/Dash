<?php
namespace lib\utility\cronjob;

class options
{

	private static function current_cronjob_path()
	{
		return '* * * * * php '. __DIR__ . '/cronjob.php';
	}


	private static function set_cronjob($_active)
	{
		$output = shell_exec('crontab -l');
		$new_crontab_txt = $output;

		if($_active)
		{
			if(self::status())
			{
				// need to active again
				return true;
			}

			$new_crontab_txt .= self::current_cronjob_path(). PHP_EOL;
		}
		else
		{
			if(!self::status())
			{
				// need to deactive again
				return true;
			}

			$new_crontab_txt = str_replace(self::current_cronjob_path(). PHP_EOL, '', $new_crontab_txt);
		}

		file_put_contents('/tmp/crontab.txt', $new_crontab_txt);
		exec('crontab /tmp/crontab.txt', $result, $return_val);
		if($return_val === 0)
		{
			return true;
		}
		else
		{
			return false;
		}
	}


	public static function active()
	{
		return self::set_cronjob(true);
	}


	public static function deactive()
	{
		return self::set_cronjob(false);
	}


	public static function status()
	{
		exec('crontab -l', $list, $return_val);
		if($return_val === 0 && is_array($list))
		{
			if(in_array(self::current_cronjob_path(), $list))
			{
				return true;
			}
			else
			{
				return false;
			}
		}
	}
}
?>