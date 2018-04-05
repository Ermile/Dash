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
		$file_addr = stream_resolve_include_path($file_addr);

		if($file_addr)
		{
			self::$required[$_class_name] = true;
			include_once($file_addr);
		}
		else
		{
			return false;
		}
	}



	// public static $require     = [];
	// public static $core_prefix = ['lib', 'mvc', 'addons'];
	// public static $autoload    = false;

	// /**
	//  * [load description]
	//  * @param  [type] $name [description]
	//  * @return [type]       [description]
	//  */
	// public static function load($name)
	// {
	// 	if(isset(self::$require[$name]))
	// 	{
	// 		return;
	// 	}
	// 	if(strpos($name, 'Twig') === 0)
	// 	{
	// 		return;
	// 	}

	// 	$split_name = preg_split("[\\\]", $name);
	// 	if(count($split_name) > 1)
	// 	{
	// 		$file_addr = self::get_file_name($split_name);
	// 		if($file_addr !== false)
	// 		{
	// 			self::$require[$name] = 1;
	// 			$file_addr = stream_resolve_include_path($file_addr);
	// 			include_once($file_addr);
	// 		}
	// 		else
	// 		{
	// 			$name = preg_replace("/[\\\]/", DIRECTORY_SEPARATOR, $name).'.php';
	// 			$file_addr = stream_resolve_include_path($name);
	// 			if($file_addr)
	// 			{
	// 				self::$require[$name] = 1;
	// 				include_once($file_addr);
	// 			}
	// 			else
	// 			{
	// 				return false;
	// 			}
	// 		}
	// 	}
	// }

	// /**
	//  * [get_file_name description]
	//  * @param  [type] $split_name [description]
	//  * @return [type]             [description]
	//  */
	// public static function get_file_name($split_name)
	// {
	// 	list($prefix, $sub_path, $exec_file) = self::file_splice($split_name);
	// 	$prefix_file = null;
	// 	if (preg_grep("/^$prefix$/", self::$core_prefix))
	// 	{
	// 		$file_addr = self::check_file($prefix, $sub_path, $exec_file);
	// 		return $file_addr;
	// 	}

	// 	$prefix_file = \lib\engine\content::get_addr();
	// 	$prefix_file = preg_replace("#\/[^\/]+\/?$#", '', $prefix_file);
	// 	$file_addr   = $prefix_file. '/'. $prefix.'/'. $sub_path. $exec_file;
	// 	if(!file_exists($file_addr))
	// 	{
	// 		$file_addr = false;
	// 	}
	// 	return $file_addr;
	// }


	// /**
	//  * [check_file description]
	//  * @param  [type] $prefix    [description]
	//  * @param  [type] $sub_path  [description]
	//  * @param  [type] $exec_file [description]
	//  * @return [type]            [description]
	//  */
	// public static function check_file($prefix, $sub_path, $exec_file)
	// {
	// 	if(!defined($prefix))
	// 	{
	// 		return false;
	// 	}
	// 	$prefix_file = constant($prefix);
	// 	$file_addr   = $prefix_file .$sub_path .$exec_file;
	// 	if(stream_resolve_include_path($file_addr))
	// 	{
	// 		return $file_addr;
	// 	}
	// 	return false;
	// }


	// /**
	//  * [file_splice description]
	//  * @param  [type] $split_name [description]
	//  * @return [type]             [description]
	//  */
	// public static function file_splice($split_name)
	// {
	// 	$prefix = $split_name[0];
	// 	array_shift($split_name);

	// 	$exec_file = end($split_name);
	// 	array_pop($split_name);

	// 	$sub_path = (count($split_name) > 0) ? join($split_name, "/") .'/' : '';

	// 	return [$prefix, $sub_path, $exec_file .".php"];
	// }
}
spl_autoload_register("\autoload::load");

// LAUNCH DASH!
\lib\engine\power::on();
?>
