<?php
namespace dash\app;


class smstemplate
{

	public static function get($_id)
	{

	}

	public static function list()
	{
		$list = [];
		if(is_callable(['\lib\smstemplate', 'list']))
		{
			return \lib\smstemplate::list();
		}

		$addr = root.'/includes/smstemplate/list.json';

		if(is_file($addr))
		{
			$json = \dash\file::read($addr);
			if($json)
			{
				$list = json_decode($json, true);
			}
		}

		return $list;
	}

	public static function set($_args, $_update = false)
	{
		if(!isset($_args['name']))
		{
			\dash\notif::error(T_("Name of template is required"), 'name');
			return false;
		}

		if(!isset($_args['text']))
		{
			\dash\notif::error(T_("Text of template is required"), 'text');
			return false;
		}

		if(!$_args['name'])
		{
			\dash\notif::error(T_("Plase fill the name of template"), 'name');
			return false;
		}


		if(!$_args['text'])
		{
			\dash\notif::error(T_("Plase fill the text of template"), 'text');
			return false;
		}

		if(mb_strlen($_args['name']) > 50)
		{
			\dash\notif::error(T_("You must set name of template less than 50 character"), 'name');
			return false;
		}

		$list = self::list();
		if(isset($list[$_args['name']]) && !$_update)
		{
			\dash\notif::error(T_("This name is reserved before, try another"), 'name');
			return false;
		}

		if($_update && !array_key_exists($_update, $list))
		{
			\dash\notif::error(T_("This template is not in your template list!"), 'name');
			return false;
		}
		if($_update)
		{
			unset($list[$_update]);
		}

		$list[$_args['name']] = $_args['text'];
		$addr                 = root.'includes/smstemplate/list.json';

		if(!is_file($addr))
		{
			\dash\file::makeDir(str_replace('/list.json', '', $addr), null , true);
		}

		$list                 = json_encode($list, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
		\dash\log::set('setSmsTemplate');
		\dash\file::write($addr, $list);

		return true;
	}


	public static function remove($_name)
	{
		$list = self::list();
		if(!isset($list[$_name]))
		{
			\dash\notif::error(T_("This template is not in your template list!"), 'name');
			return false;
		}

		unset($list[$_name]);
		$addr                 = root.'/includes/smstemplate/list.json';
		$list                 = json_encode($list, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

		\dash\log::set('removeSmsTemplate');
		\dash\file::write($addr, $list);

		return true;
	}
}
?>
