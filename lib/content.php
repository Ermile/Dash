<?php
namespace lib;
/**
 * this lib handle content of our PHP framework, Dash
 * v 0.1
 */
class content
{

	public static function check($_content_name)
	{
		// list of addons exist in dash,
		$dash_addons = ['cp', 'enter', 'api', 'su', 'account'];

		// set repository name
		$myrep = 'content_'. $_content_name;

		$is_content = null;

		// check content_aaa folder is exist in project or dash addons folder
		if(is_dir(root.$myrep))
		{
			$is_content = true;
		}
		// if exist on addons folder
		elseif(in_array($_content_name, $dash_addons) && is_dir(addons.$myrep))
		{
			$is_content = true;
		}

		return $is_content;
	}


	public static function is_content()
	{
		return self::check(...func_get_args());
	}


	public static function name()
	{
		$url_content = \lib\url::content();
		$content = 'content';
		if($url_content)
		{
			$content = $content. '_'. $url_content;
		}
		return $content;
	}
}
?>