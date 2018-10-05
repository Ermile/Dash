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
	}
}
?>