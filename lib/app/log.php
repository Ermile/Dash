<?php
namespace dash\app;


class log
{
	public static $sort_field =
	[
		'id',

	];


	/**
	 * Gets the course.
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  The course.
	 */
	public static function list($_string = null, $_args = [])
	{

		$default_meta =
		[
			'sort'  => null,
			'order' => null,
		];

		if(!is_array($_args))
		{
			$_args = [];
		}

		$_args = array_merge($default_meta, $_args);

		if($_args['sort'] && !in_array($_args['sort'], self::$sort_field))
		{
			$_args['sort'] = null;
		}


		$result            = \dash\db\logs::search($_string, $_args);
		$temp              = [];

		foreach ($result as $key => $value)
		{
			$check = self::ready($value);
			if($check)
			{
				$temp[] = $check;
			}
		}

		return $temp;
	}

	private static $caller = [];


	private static function caller_detail($_caller)
	{
		if(empty(self::$caller))
		{
			self::$caller = \dash\log::lists();
		}


		if(array_key_exists($_caller, self::$caller))
		{
			return self::$caller[$_caller];
		}
		return [];
	}


	public static function ready($_data)
	{
		if(!is_array($_data))
		{
			return false;
		}

		$result = [];

		foreach ($_data as $key => $value)
		{
			switch ($key)
			{
				case 'caller':
					$result[$key] = $value;
					$result = array_merge($result, self::caller_detail($value));
					break;

				default:
					$result[$key] = $value;
					break;
			}
		}

		return $result;

	}
}
?>