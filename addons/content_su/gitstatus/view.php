<?php
namespace content_su\gitstatus;

class view
{
	public static function config()
	{
		\dash\data::page_title(T_("Git Status"));
		\dash\data::page_desc(T_('Check file status on this project'));

		$result = self::gitStatus();

		if(is_array($result))
		{
			foreach ($result as $key => $value)
			{
				echo $value;
			}
		}
		echo "<hr>";

		$result = self::gitDiff();

		if(is_array($result))
		{
			foreach ($result as $key => $value)
			{
				echo $value;
			}
		}

		\dash\code::exit();
	}


	public static function gitStatus()
	{
		$location = null;
		$result   = [];

		// pull dash
		if(is_dir(root. 'dash'))
		{
			$location = '../dash';
		}
		elseif(is_dir(root. '../dash'))
		{
			$location = '../../dash';
		}

		$result[] = "<h1>Dash</h1>";
		$result[] =  \dash\utility\git::gitstatus($location);

		// pull current project
		$name = \dash\url::root();
		$location = '../../'. $name;

		$result[] = "<h1>$name <small>Current Project</small></h1>";
		$result[] =  \dash\utility\git::gitstatus($location);

		return $result;
	}

	public static function gitDiff()
	{
		$location = null;
		$result   = [];

		// pull dash
		if(is_dir(root. 'dash'))
		{
			$location = '../dash';
		}
		elseif(is_dir(root. '../dash'))
		{
			$location = '../../dash';
		}

		$result[] = "<h1>Dash</h1>";
		$result[] =  \dash\utility\git::gitdiff($location);

		// pull current project
		$name = \dash\url::root();
		$location = '../../'. $name;

		$result[] = "<h1>$name <small>Current Project</small></h1>";
		$result[] =  \dash\utility\git::gitdiff($location);

		return $result;
	}


}
?>