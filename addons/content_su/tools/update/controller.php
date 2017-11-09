<?php
namespace addons\content_su\tools\update;

class controller extends \addons\content_su\main\controller
{
	public function ready()
	{
		parent::ready();
		$this->updateGitRepo();
		$this->get()->ALL();
	}


	public function updateGitRepo()
	{
		// declare variables
		$exist    = true;
		$rep      = null;
		$location = null;
		$name     = \lib\utility::get('git');
		if(!$name)
		{
			return;
		}

		// switch by name of repository
		switch ($name)
		{
			case 'dash':
				// $rep      .= "https://github.com/Ermile/dash.git";
				$location = '../../dash';
				echo \lib\utility\git::pull($location);
				break;


			case 'all':
				// pull dash
				$location = '../../dash';
				echo "<h1>Dash</h1>";
				echo \lib\utility\git::pull($location);

				// pull current project
				$name = Domain;
				$location = '../../'. $name;
				echo "<h1>Current Project $name</h1>";
				echo \lib\utility\git::pull($location);
				break;

			case '':
				break;

			default:
				$location = '../../'. $name;
				echo \lib\utility\git::pull($location);
				// $exist = false;
				// return;
				break;
		}
		\lib\code::exit();
	}
}
?>