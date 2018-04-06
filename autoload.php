<?php
/**
 * require default define
 */
require_once (__DIR__.'/lib/engine/define.php');


class autoload
{
	private static $required     = [];


	public static function load($_class_name)
	{
		if(isset(self::$required[$_class_name]))
		{
			return;
		}

		$addr = null;

		if(substr($_class_name, 0, 4) === 'dash')
		{
			$addr = core. 'lib';
			$addr = $addr. str_replace('dash', '', $_class_name);;
			$addr = self::os_path($addr);
			$addr = $addr. '.php';

			if(self::open($addr))
			{
				self::$required[$_class_name] = true;
			}
		}
		elseif(substr($_class_name, 0, 7) === 'content')
		{
			$addr = root. $_class_name;
			$addr = self::os_path($addr);
			$addr = $addr. '.php';

			if(self::open($addr))
			{
				self::$required[$_class_name] = true;
			}
			else
			{
				$addr = addons. $_class_name;
				$addr = self::os_path($addr);
				$addr = $addr. '.php';
				if(self::open($addr))
				{
					self::$required[$_class_name] = true;
				}
			}
		}
		elseif(substr($_class_name, 0, 3) === 'lib')
		{
			$addr = root. 'includes/'. $_class_name;
			$addr = self::os_path($addr);
			$addr = $addr. '.php';
			if(self::open($addr))
			{
				self::$required[$_class_name] = true;
			}
		}
	}


	private static function open($_addr)
	{
		if(is_file($_addr))
		{
			include_once($_addr);
			return true;
		}
		return false;
	}


	private static function os_path($_addr)
	{
		$_addr = str_replace('\\', DIRECTORY_SEPARATOR, $_addr);
		$_addr = str_replace('/', DIRECTORY_SEPARATOR, $_addr);
		return $_addr;
	}
}

spl_autoload_register("\autoload::load");

// LAUNCH DASH!
\dash\engine\power::on();
?>
