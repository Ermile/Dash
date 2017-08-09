<?php
namespace ilib\api;
use \lib\router;

class config extends \lib\api\config
{
	/**
	 * check status of permission to access special module
	 * @param  [type] $route [description]
	 * @return [type]        [description]
	 */
	public function check($route)
	{
		$mysub = router::get_sub_domain();
		if(router::get_sub_domain() === "cp")
		{
			$mymodule  = router::get_url(0);
			$mychild   = router::get_url(1);
			$mymethod  = $this->api_method;
			$myrequest = 'view';
			$myblock   = 'block';
			if(strrpos($mychild,'=') !== false)
			{
				$mychild = substr($mychild, 0, strrpos($mychild, '='));
			}

			// set request name by type of it
			switch ($mymethod)
			{
				case 'get':
					$myrequest = 'view';
					break;

				case 'post':
					$myrequest = 'add';
					$myblock   = 'notify';
					break;

				case 'put':
					$myrequest = 'edit';
					$myblock   = 'notify';
					break;

				case 'delete':
					$myrequest = 'delete';
					$myblock   = 'notify';
					break;

				default:
					$myrequest = '#invalid';
					break;
			}
			// find request by 2th slash in url named as child
			switch ($mychild)
			{
				case 'add':
					$myrequest = 'add';
					break;

				case 'edit':
					$myrequest = 'edit';
					break;

				case 'delete':
					$myrequest = 'delete';
					break;
			}

			// set some setting for special module
			switch ($mymodule)
			{
				case 'posts':
				case 'pages':
				case 'users':
				case 'options':
				case 'permissions':
				case 'tags':
				case 'cats':
					break;

				case 'profile':
					$myblock   = false;
					break;

				default:
					break;
			}
			// Check permission and if user can do this operation
			// allow to do it, else show related message in notify center
			$myController = \lib\main::$controller;
			$myController->access($mysub, $mymodule, $myrequest, $myblock);
		}

		parent::check(...func_get_args());
	}
}
?>