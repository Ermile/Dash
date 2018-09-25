<?php
namespace dash\engine\cronjob;

class options
{

	public static function current_cronjob_path()
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

		file_put_contents(__DIR__.'/crontab.txt', $new_crontab_txt);
		exec('crontab '. __DIR__.'/crontab.txt', $result, $return_val);
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


	private static function load_list_file($_addr)
	{
		$list = [];

		if(is_file($_addr))
		{
			$list = file_get_contents($_addr);
			$list = json_decode($list, true);
			if(!is_array($list))
			{
				$list = [];
			}
		}
		return $list;
	}



	public static function list()
	{
		$dash_list    = self::load_list_file(__DIR__. '/cronjob.json');
		$project_list = self::load_list_file(root. 'includes/cronjob/list.json');
		$saved_list   = self::load_list_file(root. 'list.crontab.txt');

		$list = array_merge($dash_list, $project_list);

		foreach ($list as $key => $value)
		{
			if(array_key_exists($key, $saved_list))
			{
				$list[$key]['active'] = true;
				if(isset($saved_list[$key]['url']))
				{
					$list[$key]['saved_url'] = $saved_list[$key]['url'];
				}
			}
		}

		return $list;
	}


	public static function save_list($_list)
	{
		if(!is_array($_list))
		{
			return false;
		}

		$master_list = self::list();

		if(!$master_list)
		{
			return;
		}

		$save = [];
		foreach ($master_list as $key => $value)
		{
			if(array_key_exists($key, $_list) && $_list[$key] && isset($value['url']))
			{
				$save[$key] =
				[
					'url' => \dash\url::base(). '/hook/cronjob?type='. $value['url'],
				];
			}
		}

		$save = json_encode($save, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
		file_put_contents(root. 'list.crontab.txt', $save);
		return true;
	}
}
?>