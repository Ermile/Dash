<?php
namespace content_su\command;

class model
{
	public static function post()
	{
		$password = \dash\request::post('sudo');
		if(!$password)
		{
			\dash\notif::error(T_("Please fill the password"), 'password');
			return false;
		}

		$btn = \dash\request::post('command');
		$command = [];
		switch ($btn)
		{
			case 'nginxrestart':
				$command[] = " service nginx restart ";
				break;

			case 'mysqlrestart':
				$command[] = " service mysql stop ";
				$command[] = " service mysql start ";
				break;

			case 'sshpass':
				$command[] = " apt-get install sshpass ";
				break;

			case 'soap':
				$command[] = " apt-get install php7.1-soap ";
				break;

			case 'soap72':
				$command[] = " apt-get install php7.2-soap ";
				break;

			case 'update':
				$command[] = " apt-get update ";
				break;

			case 'upgrade':
				$command[] = " apt-get upgrade ";
				break;

			default:
				\dash\notif::error(T_("Invalid command"));
				return false;
				break;
		}

		$resultBool = true;
		$echo = [];
		if(!empty($command))
		{

			foreach ($command as $key => $value)
			{
				$exec = " echo '$password' | sudo -S ". $value;
				$echo[] = " sudo ". $value;

				exec($exec, $result);

				if(!$result && $resultBool)
				{
					$resultBool = false;
				}
			}
		}

		if($resultBool)
		{
			\dash\notif::ok(implode(' ; ', $echo). " Sucessfully run!");
		}
		else
		{
			\dash\notif::error(implode(' ; ', $echo). " unsuccess!");
		}

	}


}
?>