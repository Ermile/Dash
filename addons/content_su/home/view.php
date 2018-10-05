<?php
namespace content_su\home;

class view
{
	public static function config()
	{
		\dash\data::page_title(T_("Supervisor dashboard"));
		\dash\data::page_desc(T_("Hey there!"));

		\dash\log::set('loadSU');

		\dash\data::page_pictogram('gauge');

		// pull dash
		if(is_dir(root. 'dash'))
		{
			$location = '../dash';
		}
		elseif(is_dir(root. '../dash'))
		{
			$location = '../../dash';
		}

		$we_have_change = false;

		\dash\utility\git::gitdiff($location);
		$gitdiff = \dash\temp::get('git_diff_change');

		if(!$gitdiff)
		{
			\dash\utility\git::gitdiff(root);
			$gitdiff = \dash\temp::get('git_diff_change');

			if(!$gitdiff)
			{
				// no change
			}
			else
			{
				$we_have_change = true;
			}

		}
		else
		{
			$we_have_change = true;
		}

		\dash\data::gitHaveChange($we_have_change);

		// get last update
		\dash\data::dash_lastUpdate(\dash\utility\git::getLastUpdate());
		\dash\data::dash_projectVersion(\dash\utility\git::getLastUpdate(false));

		if(\dash\data::dash_lastUpdate() > \dash\data::dash_projectVersion())
		{
			\dash\data::su_lastUpdate(\dash\data::dash_lastUpdate());
		}
		else
		{
			\dash\data::su_lastUpdate(\dash\data::dash_projectVersion());
		}

		// // get ram value
		// exec("free -mtl", $ramCapacity);
		// \dash\data::su_ram($ramCapacity);

		// get uptime
		$uptime = shell_exec('uptime -p');
		\dash\data::su_uptime($uptime);

		// get disk total size
		\dash\data::su_disk(\dash\utility\upload::readableSize(disk_total_space("/")));
		\dash\data::su_diskFree(\dash\utility\upload::readableSize(disk_free_space("/")));



	}

	private static function roundsize($size)
	{
		$i   = 0;
		$iec = array("B", "Kb", "Mb", "Gb", "Tb");
	    while (($size/1024)>1)
	    {
	        $size = $size/1024;
	        $i++;
	    }
	    return(round($size,1). " ". $iec[$i]);
	}
}
?>