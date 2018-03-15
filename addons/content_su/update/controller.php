<?php
namespace addons\content_su\update;

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
		$exist        = true;
		$rep          = null;
		$location     = null;
		$name         = \lib\request::get('git');
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
				$name = \lib\url::root();
				$location = '../../'. $name;
				echo "<h1>$name <small>Current Project</small></h1>";
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
		echo \lib\utility\git::pull($dashLocation);
	}

}
?>