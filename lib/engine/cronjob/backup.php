<?php
class backup
{

	/**
	 * exec all finded backup.php
	 */
	public function run()
	{
		$dir = __DIR__;
		$dir = str_replace('dash/lib/engine/cronjob', '', $dir);
		$dir .= 'includes/database/backup/schedule';

		if(is_file($dir))
		{
			$this->clean($dir);
			$this->run_backup($dir);
		}
	}


	public function schedule_file($_schedule_path)
	{
		$schedule = file_get_contents($_schedule_path);
		$schedule = json_decode($schedule, true);
		if($schedule && is_array($schedule))
		{
			return $schedule;
		}
		return false;
	}


	public function run_backup($_schedule_path)
	{
		$schedule = $this->schedule_file($_schedule_path);
		if(!$schedule)
		{
			return;
		}

		if(!array_key_exists('auto_backup', $schedule) || !array_key_exists('every', $schedule) || !array_key_exists('time', $schedule))
		{
			return;
		}

		if(!$schedule['auto_backup'] || !$schedule['every'] || !$schedule['time'])
		{
			return;
		}

		if($schedule['every'] != 'hour' && date("H") !== $schedule['time'])
		{
			return;
		}

		$left_time = $this->get_left_time($schedule['every'], $schedule['time']);

		if($left_time === false)
		{
			return;
		}

		$url         = preg_replace("/schedule$/", 'files/', $_schedule_path);
		$files       = glob($url . "*.*");
		$file_m_time = array_map(function($_a){return filemtime($_a);}, $files);

		arsort($file_m_time);

		$file_m_time = array_values($file_m_time);

		if(isset($file_m_time[0]))
		{
			$old_time = $file_m_time[0];

			if(time() - $old_time >= $left_time)
			{
				$this->backup_dump_exec($_schedule_path);
			}
			else
			{
				return;
			}
		}
		else
		{
			$this->backup_dump_exec($_schedule_path);
		}
	}


	public function backup_dump_exec($_schedule_path)
	{
		$root_url = preg_replace("/includes(.*)$/", '', $_schedule_path);

		$schedule = $this->schedule_file($_schedule_path);
		if(!$schedule)
		{
			return;
		}

		if(isset($schedule['db_name']))
		{
			$project_name = $schedule['db_name'];
		}
		else
		{
			return false;
		}

		if(file_exists($root_url. 'config.me.php'))
		{
			require_once($root_url. 'config.me.php');
		}
		elseif(file_exists($root_url. 'config.php'))
		{
			require_once($root_url. 'config.php');
		}

		if(defined('db_user') && defined('db_pass'))
		{
			$db_host    = 'localhost';
			$db_charset = 'utf8mb4';
			$date       = date('Y-m-d_H-i-s');
			$dest_file  = $project_name.'_'. $date. '.sql.bz2';
			$url        = preg_replace("/schedule$/", 'files/', $_schedule_path);
			$dest_dir   = $url;
			// create folder if not exist
			if(!is_dir($dest_dir))
			{
				mkdir($dest_dir, 0755, true);
			}

			$cmd  = "mysqldump --single-transaction --add-drop-table";
			$cmd .= " --skip-lock-tables ";
			$cmd .= " --host='$db_host' --set-charset='$db_charset'";
			$cmd .= " --user='".db_user."'";
			$cmd .= " --password='".db_pass."' '". $project_name."'";
			$cmd .= " | bzip2 -c > $dest_dir$dest_file";

			// to import this file
			// bunzip2 < filename.sql.bz2 | mysql -u root -p $project_name
			$result     = exec($cmd, $output, $return_var);
			if($return_var ===  0 )
			{
				$log_txt  = '';
				$log_txt .= $date;
				$log_txt .= ' - complete at: ';
				$log_txt .= date("Y-m-d_H-i-s");
				$log_txt .= ' - file_name: '. $dest_file;
				$this->save_log($_schedule_path, $log_txt);
			}
		}
	}


	public function clean($_schedule_path)
	{
		$schedule = $this->schedule_file($_schedule_path);
		if(!$schedule)
		{
			return;
		}

		if(!array_key_exists('life_time', $schedule))
		{
			return;
		}

		$left_time = $this->get_left_time($schedule['life_time']);
		if($left_time === false)
		{
			return;
		}

		$url         = preg_replace("/schedule$/", 'files/', $_schedule_path);
		$files       = glob($url . "*.*");
		$must_remove = [];

		foreach ($files as $key => $value)
		{
			if(time() - filemtime($value) > $left_time)
			{
				array_push($must_remove, $value);
			}
		}
		if($must_remove)
		{
			$cmd = "rm ". implode(' ', $must_remove);
			$result     = exec($cmd, $output, $return_var);
			if($return_var ===  0 )
			{
				$log_txt = '---------------'. "\n";
				$log_txt .= 'Autoremove at: ';
				$log_txt .= date("Y-m-d_H-i-s");
				$log_txt .= ' - files: '. "\n" . implode("\n", $must_remove);
				$log_txt .= "\n";
				$this->save_log($_schedule_path, $log_txt);
			}
		}
	}


	public function get_left_time($_time, $_time2 = null)
	{
		$left_time = 1;

		switch ($_time)
		{
			case 'year2':
				$left_time *= 60 * 60 * 24 * 365 * 2;
				break;

			case 'year':
				$left_time *= 60 * 60 * 24 * 365;
				break;

			case 'month6':
				$left_time *= 60 * 60 * 24 * 30 * 6;
				break;

			case 'month3':
				$left_time *= 60 * 60 * 24 * 30 * 3;
				break;

			case 'month2':
				$left_time *= 60 * 60 * 24 * 30 * 2;
				break;

			case 'month':
				$left_time *= 60 * 60 * 24 * 30;
				break;

			case 'week':
				$left_time *= 60 * 60 * 24 * 7;
				break;

			case 'week2':
				$left_time *= 60 * 60 * 24 * 7 * 2;
				break;

			case 'day':
				$left_time *= 60 * 60 * 24;
				break;

			case 'day3':
				$left_time *= 60 * 60 * 24 * 3;
				break;

			case 'day5':
				$left_time *= 60 * 60 * 24 * 5;
				break;

			case 'hour':
				$left_time *= 60 * 60 * intval($_time2);
				if($left_time === 0)
				{
					$left_time = 60 * 60 * 1;
				}
				break;

			default:
				return false;
				break;
		}
		return $left_time;
	}


	public function save_log($_schedule_path, $_text)
	{
		$_text    = "--------------- \n $_text \n";
		$log_file = preg_replace("/schedule$/", 'log', $_schedule_path);
		file_put_contents( $log_file, $_text, FILE_APPEND );
	}
}
?>