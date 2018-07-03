<?php
namespace dash\utility;


class server
{
	public static function cpu_usage()
	{
		$cpu = self::cpu_detail();
		if(isset($cpu['percent']))
		{
			return $cpu['percent'];
		}
		return false;
	}


	public static function cpu_detail()
	{
		$cmd = "cat /proc/cpuinfo 2>&1";
		exec($cmd, $output, $return_val);
		if ($return_val === 0)
		{
			$return          = [];
			$return['title'] = null;

			// find model name
			foreach ($output as $value)
			{
				if (preg_match("/model name.+:(.*)/i", $value, $match))
				{
					$return['title'] = trim($match[1]);
					break;
				}
			}

			$return['percent'] = intval(self::getServerLoad());
			$return['desc'] = $output;

			return $return;
		}
		else
		{
			return false;
		}
	}

	public static function disk_usage()
	{
		$disc = self::disk_detail();
		if(isset($disc['percent']))
		{
			return $disc['percent'];
		}
		return false;
	}


	public static function disk_detail()
	{
		$cmd = "df -h 2>&1";
		exec($cmd, $output, $return_val);
		if ($return_val === 0)
		{
			$return['percent'] = 0;
			foreach ($output as $value)
			{
				if(preg_match("/([0-9]+)% \/$/i", $value, $match))
				{
					$return['percent'] = intval($match[1]);
					break;
				}
			}
			$return['title']   = "Usage of {$return['percent']}%";
			$return['desc']    = $output;
			$return['command'] = $cmd;
			return $return;
		}
		else
		{
			return false;
		}
	}


	public static function memory_usage()
	{
		if (stristr(PHP_OS, "win"))
		{
			$memory = self::memoryWin_detail();
			if(isset($memory['percent']))
			{
				return $memory['percent'];
			}
			return false;
		}
		else
		{
			$memory = self::memoryLinux_detail();
			if(isset($memory['percent']))
			{
				return $memory['percent'];
			}
			return false;
		}
	}


	public static function memory_detail()
	{
		if (stristr(PHP_OS, "win"))
		{
			return self::memoryWin_detail();
		}
		else
		{
			return self::memoryLinux_detail();
		}
	}



	public static function memoryWin_detail()
	{
		// total memory of system
		@exec('wmic memorychip get capacity', $eachMemory);
		$totalMemory = array_sum($eachMemory);
		$totalMemory = self::humanFileSize($totalMemory);

		$totalMemory = self::humanFileSize(memory_get_usage());

		// var_dump($totalMemory);
		return $totalMemory;
	}


	public static function memoryLinux_detail()
	{
		$cmd = "free 2>&1";
		exec($cmd, $output, $return_val);
		if ($return_val === 0) {

			$return = [];

			$return['memTotalBytes'] = 0;
			$return['memUsedBytes']  = 0;
			$return['memFreeBytes']  = 0;

			if (preg_match("/Mem: *([0-9]+) *([0-9]+) *([0-9]+) */i", $output[1], $match))
			{
				$return['memTotalBytes'] = $match[1]*1024;
				$return['memUsedBytes']  = $match[2]*1024;
				$return['memFreeBytes']  = $match[3]*1024;
				$onePc                   = $return['memTotalBytes'] / 100;
				$return['memTotal']      = self::humanFileSize($return['memTotalBytes']);
				$return['memUsed']       = self::humanFileSize($return['memUsedBytes']);
				$return['memFree']       = self::humanFileSize($return['memFreeBytes']);
				$return['percent']       = intval($return['memUsedBytes'] / $onePc);
				$return['title']         = "Total: {$return['memTotal']} | Free: {$return['memFree']} | Used: {$return['memUsed']}";
			}
			return $return;
		}
		else
		{
			return false;
		}
	}




	public static function temperature_usage()
	{
		$temperature = self::temperature();
		if(isset($temperature['percent']))
		{
			return $temperature['percent'];
		}
		return false;
	}


	public static function temperature()
	{
		$cmd = "cat /sys/class/thermal/thermal_zone0/temp 2>&1";
		exec($cmd, $output, $return_val);
		if ($return_val === 0)
		{
			$return['title']   = "t° Raspberry";
			$return['success'] = 1;
			$return['output']  = $output;
			$return['command'] = $cmd;
			$return['percent'] = 0;
			$return['percent'] = $output[0]/1000;
			return $return;
		}
		else
		{
           return false;
		}
	}


	private static function humanFileSize($size, $unit = "")
	{
		if ((!$unit && $size >= 1 << 30) || $unit == "GB")
		{
			return number_format($size / (1 << 30), 2) . " GB";
		}

		if ((!$unit && $size >= 1 << 20) || $unit == "MB")
		{
			return number_format($size / (1 << 20), 2) . " MB";
		}

		if ((!$unit && $size >= 1 << 10) || $unit == "KB")
		{
			return number_format($size / (1 << 10), 2) . " KB";
		}

		return number_format($size) . " bytes";
	}


	private static function _getServerLoadLinuxData()
	{
		if (is_readable("/proc/stat"))
		{
			$stats = @file_get_contents("/proc/stat");

			if ($stats !== false)
			{
				// Remove double spaces to make it easier to extract values with explode()
				$stats = preg_replace("/[[:blank:]]+/", " ", $stats);

				// Separate lines
				$stats = str_replace(array("\r\n", "\n\r", "\r"), "\n", $stats);
				$stats = explode("\n", $stats);

				// Separate values and find line for main CPU load
				foreach ($stats as $statLine)
				{
					$statLineData = explode(" ", trim($statLine));

					// Found!
					if
					(
						(count($statLineData) >= 5) &&
						($statLineData[0] == "cpu")
					)
					{
						return array(
							$statLineData[1],
							$statLineData[2],
							$statLineData[3],
							$statLineData[4],
						);
					}
				}
			}
		}

		return null;
	}

	// Returns server load in percent (just number, without percent sign)
	public static function getServerLoad()
	{
		$load = null;

		if (stristr(PHP_OS, "win"))
		{
			$cmd = "wmic cpu get loadpercentage /all";
			@exec($cmd, $output);

			if ($output)
			{
				foreach ($output as $line)
				{
					if ($line && preg_match("/^[0-9]+\$/", $line))
					{
						$load = $line;
						break;
					}
				}
			}
		}
		else
		{
			if (is_readable("/proc/stat"))
			{
				// Collect 2 samples - each with 1 second period
				// See: https://de.wikipedia.org/wiki/Load#Der_Load_Average_auf_Unix-Systemen
				$statData1 = self::_getServerLoadLinuxData();
				\dash\code::sleep(1);
				$statData2 = self::_getServerLoadLinuxData();

				if
				(
						(!is_null($statData1)) &&
						(!is_null($statData2))
				) {
					// Get difference
					$statData2[0] -= $statData1[0];
					$statData2[1] -= $statData1[1];
					$statData2[2] -= $statData1[2];
					$statData2[3] -= $statData1[3];

					// Sum up the 4 values for User, Nice, System and Idle and calculate
					// the percentage of idle time (which is part of the 4 values!)
					$cpuTime = $statData2[0] + $statData2[1] + $statData2[2] + $statData2[3];

					if($cpuTime === 0)
					{

					}
					else
					{
						// Invert percentage to get CPU time, not idle time
						$load = 100 - ($statData2[3] * 100 / $cpuTime);
					}
				}
			}
		}

		return $load;
	}
}
?>