<?php
/**
 * require default define
 */
require_once (__DIR__.'/lib/engine/define.php');

/**
* include all file needed
*/
class autoload
{
	private static $required = [];

	public static function load($_class_name)
	{
		if(isset(self::$required[$_class_name]))
		{
			return;
		}

		if(strpos($_class_name, 'Twig') === 0)
		{
			return;
		}

		$file_addr = str_replace('\\', DIRECTORY_SEPARATOR, $_class_name);
		$file_addr = $file_addr. '.php';
		$real_addr = stream_resolve_include_path($file_addr);

		if($real_addr)
		{
			self::$required[$_class_name] = true;
			include_once($real_addr);
		}
		else
		{
			$real_addr = stream_resolve_include_path(root. $file_addr);
			if($real_addr)
			{
				self::$required[$_class_name] = true;
				include_once($real_addr);
			}
			else
			{
				$split = explode('\\', $_class_name);
				if(isset($split[0]))
				{
					$core_prefix = $split[0];
					if(defined($core_prefix))
					{
						$real_addr = stream_resolve_include_path(constant($core_prefix). $file_addr);
						if($real_addr)
						{
							self::$required[$_class_name] = true;
							include_once($real_addr);
						}
					}
				}
			}
		}
	}
}

spl_autoload_register("\autoload::load");

// LAUNCH DASH!
\lib\engine\power::on();
?>
