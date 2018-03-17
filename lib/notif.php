<?php
namespace lib;
/**
 * Class for notif.
 */
class notif
{
	/**
	 * static var
	 *
	 * @var        array
	 */
	private static $error    = array();
	private static $warn     = array();
	private static $true     = array();
	private static $msg      = array();
	private static $property = array();
	private static $form     = array();
	private static $check    = true;
	private static $result   = null;
	private static $title;

	/**
	 * STATUS
	 * 0 => error
	 * 1 => true
	 * 2 => warn
	 *
	 * @var        array
	 */
	public static  $status   = 1;


	/**
	 * create error message (fatal)
	 * @param  [type]  $_error   [description]
	 * @param  boolean $_element [description]
	 * @param  string  $_group   [description]
	 * @return [type]            [description]
	 */
	public static function error($_error, $_element = false, $_group = 'public')
	{
		self::$check = false;
		self::$status = 0;
		array_push(self::$error, array('title' => $_error, "element" => $_element, "group" => $_group));
	}


	/**
	 * create warn message
	 * @param  [type]  $_error   [description]
	 * @param  boolean $_element [description]
	 * @param  string  $_group   [description]
	 * @return [type]            [description]
	 */
	public static function warn($_error, $_element = false, $_group = 'public')
	{
		if(self::$check)
		{
			self::$status = 2;
		}
		array_push(self::$warn,	array('title' => $_error, "element" => $_element, "group" => $_group));
	}


	/**
	 * create true message (successful)
	 * @param  [type]  $_error   [description]
	 * @param  boolean $_element [description]
	 * @param  string  $_group   [description]
	 * @return [type]            [description]
	 */
	public static function true($_error, $_element = false, $_group = 'public')
	{
		array_push(self::$true,	array('title' => $_error, "element" => $_element, "group" => $_group));
	}


	/**
	 * { function_description }
	 *
	 * @param      <type>  $_title  The title
	 */
	public static function title($_title)
	{
		self::$title = $_title;
	}


	/**
	 * set msg for showing data with ajax on pages
	 * @param  [string or array] $_name  if array we seperate it in many msg else it's name of msg
	 * @param  [string or array] $_value if pass
	 * @param  [bool]            $_reset
	 * @return set global value
	 */
	public static function msg($_name, $_value = null, $_reset = null)
	{
		if($_reset)
		{
			self::$msg = array();
		}

		if(is_array($_name))
		{
			foreach($_name as $key => $value)
			{
				self::$msg[$key] = $value;
			}
		}
		else
		{
			if($_value !== false && $_value !== null)
			{
				self::$msg[$_name] = $_value;
			}
			else
			{
				array_push(self::$msg, $_name);
			}
		}
	}


	/**
	 * set property for notif
	 * @param  [type]  $_property [description]
	 * @param  boolean $_value    [description]
	 * @return [type]             [description]
	 */
	public static function property($_property, $_value = false)
	{
		if(is_array($_property))
		{
			foreach ($_property as $key => $value)
			{
				self::$property[$key] = $value;
			}
		}
		else
		{
			if($_value !== false)
			{
				self::$property[$_property] = $_value;
			}
			else
			{
				array_push(self::$property, $_property);
			}
		}
	}

	/**
	 * set result
	 *
	 * @param      array  $_result  The result
	 */
	public static function result($_result)
	{
		self::$result = $_result;
	}


	/**
	 * set form of messages
	 * @param  [type] $_form [description]
	 * @return [type]        [description]
	 */
	public static function form($_form)
	{
		if(!array_search($_form, self::$form))
		{
			self::$form[] = $_form;
		}
	}


	/**
	 * compile message and return it for show in page
	 * @param  boolean $_json convert return value to json or not
	 * @return [string]       depending on condition return json or string
	 */
	public static function compile($_json = false)
	{
		$notif           = array();
		$notif['status'] = self::$status;
		$notif['title']  = self::$title;
		$messages        = array();
		if(count(self::$error) > 0) $messages['error'] = self::$error;
		if(count(self::$warn) > 0)  $messages['warn']  = self::$warn;
		if(count(self::$msg) > 0)   $notif['msg']      = self::$msg;
		if(count(self::$property) > 0)
		{
			foreach (self::$property as $key => $value)
			{
				$notif[$key] = $value;
			}
		}
		if(self::$result !== null && self::$result !== false)
		{
			$notif['result'] = self::$result;
		}

		if(count(self::$form) > 0) $notif['msg']['form'] = self::$form;
		if(count(self::$true) > 0 || count($notif) == 0) $messages['true'] = self::$true;
		if(count($messages) > 0) $notif['messages'] = $messages;
		return ($_json)? json_encode($notif) : $notif;
	}


	/**
	 * get items
	 *
	 * @param      <type>  $_property  The property
	 * @param      <type>  $_args      The arguments
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function get($_property, $_args = null)
	{
		$return = [];
		if(isset(self::${$_property}))
		{
			$return = self::${$_property};
		}

		if(is_null($_args))
		{
			return $return;
		}
		elseif(isset($return[$_args]))
		{
			return $return[$_args];
		}
		return null;
	}
}
?>
