<?php
namespace content_su\update;

class controller
{
	public static function routing()
	{
		// declare variables
		$exist        = true;
		$rep          = null;
		$location     = null;
		$name         = \dash\request::get('git');
		if(!$name)
		{
			return;
		}



		// switch by name of repository
		switch ($name)
		{
			case 'dash':
				self::updateDash();
				break;


			case 'all':
				// pull dash
				self::updateDash();

				// pull current project
				$name = \dash\url::root();
				$location = '../../'. $name;
				echo "<h1>$name <small>Current Project</small></h1>";
				echo \dash\utility\git::pull($location);
				break;

			case '':
				break;

			default:
				$location = '../../'. $name;
				echo \dash\utility\git::pull($location);
				// $exist = false;
				// return;
				break;
		}
		\dash\code::exit();
	}



	public function updateDash()
	{
		$dashLocation = null;
		// check dash location
		if(is_dir(root. 'dash'))
		{
			$dashLocation = '../dash';
		}
		elseif(is_dir(root. '../dash'))
		{
			$dashLocation = '../../dash';
		}

		echo "<h1>Dash</h1>";
		echo \dash\utility\git::pull($dashLocation);
	}

}
?>