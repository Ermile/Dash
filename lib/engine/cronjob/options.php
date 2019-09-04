<?php
namespace dash\engine\cronjob;

class options
{
	public static $cronjob_folder = 'includes/cronjob';


	private static function cronjob_folder($_file_name)
	{
		$addr = root. self::$cronjob_folder;
		if(!is_dir($addr))
		{
			\dash\file::makeDir($addr);
		}
		$addr .= '/'. $_file_name;
		return $addr;
	}


	public static function current_cronjob_path()
	{
		return '* * * * * php '. __DIR__ . '/cronjob.php';
	}


	public static function unixcrontab()
	{
		return shell_exec('crontab -l');
	}


	public static function tokenjson()
	{
		$addr = self::cronjob_folder('token.me.json');

		if(is_file($addr))
		{
			return \dash\file::read($addr);
		}
		return null;
	}


	public static function masterurl()
	{
		$addr = __DIR__. '/masterurl.me.txt';

		if(is_file($addr))
		{
			return \dash\file::read($addr);
		}
		return null;
	}


	private static function set_cronjob($_active)
	{
		$output          = shell_exec('crontab -l');
		$new_crontab_txt = $output;
		$set_active      = false;

		if($_active)
		{
			if(self::status())
			{
				// needless to active again
				return true;
			}

			$set_active = true;

			$new_crontab_txt .= self::current_cronjob_path(). PHP_EOL;
		}
		else
		{
			if(!self::status())
			{
				// needless to deactive again
				return true;
			}

			$new_crontab_txt = str_replace(self::current_cronjob_path(). PHP_EOL, '', $new_crontab_txt);
		}

		\dash\file::write(__DIR__.'/crontab.txt', $new_crontab_txt);
		exec('crontab '. __DIR__.'/crontab.txt', $result, $return_val);

		if($return_val === 0)
		{
			if($set_active)
			{
				$masterurl = \dash\url::site(). '/hook/cronjob/exec';
				\dash\file::write(__DIR__. '/masterurl.me.txt', $masterurl);
			}
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
		$project_list = self::load_list_file(self::cronjob_folder('list.json'));
		$saved_list   = self::load_list_file(self::cronjob_folder('execlist.me.json'));

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
		\dash\file::write(self::cronjob_folder('execlist.me.json'), $save);
		return true;
	}
}
?>