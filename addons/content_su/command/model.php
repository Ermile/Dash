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
				\dash\log::set('commandNginxRestart');
				$command[] = " service nginx restart ";
				break;

			case 'mysqlrestart':
				\dash\log::set('commandMysqlRestart');
				$command[] = " service mysql stop ";
				$command[] = " service mysql start ";
				break;

			case 'sshpass':
				\dash\log::set('commandInstallSshpass');
				$command[] = " apt-get install sshpass ";
				break;

			default:
				\dash\log::set('commandInvalidCommand', ['command' => $btn]);
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