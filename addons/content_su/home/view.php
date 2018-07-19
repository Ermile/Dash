<?php
namespace content_su\home;

class view
{
	public static function config()
	{
		\dash\data::page_title(T_("Supervisor dashboard"));
		\dash\data::page_desc(T_("Hey there!"));

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

		if($gitdiff === 0)
		{
			\dash\utility\git::gitdiff(root);
			$gitdiff = \dash\temp::get('git_diff_change');
			if($gitdiff === 0)
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
	}
}
?>