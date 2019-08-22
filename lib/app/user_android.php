<?php
namespace dash\app;


class user_android
{


	public static $sort_field =
	[
		'id',
		'user_id',
		'uniquecode',
		'osversion',
		'version',
		'serial',
		'model',
		'manufacturer',
		'language',
		'status',
		'meta',
		'lastupdate',
		'datecreated',
	];

	public static function dataTableList(&$dataTable)
	{
		$id = array_column($dataTable, 'id');
		$id = array_map(['\\dash\\coding', 'decode'], $id);
		if(!$id)
		{
			return;
		}
		$load = \dash\db\user_android::get_dataTable(implode(',', $id));
		if(!is_array($load))
		{
			return;
		}

		$load = array_combine(array_column($load, 'user_id'), $load);

		foreach ($dataTable as $key => $value)
		{
			$myId = \dash\coding::decode($value['id']);
			if(isset($load[$myId]))
			{
				$dataTable[$key]['android_uniquecode'] = $load[$myId]['uniquecode'];
			}
		}
	}



	public static function get($_id)
	{
		$id = \dash\coding::decode($_id);
		if(!$id)
		{
			return false;
		}

		$result = \dash\db\user_android::get(['id' => $id, 'limit' => 1]);
		$temp = [];
		if(is_array($result))
		{
			$temp = self::ready($result);
		}
		return $temp;
	}



	/**
	 * ready data of user to load in api
	 *
	 * @param      <type>  $_data  The data
	 */
	public static function ready($_data)
	{
		$result = [];

		foreach ($_data as $key => $value)
		{
			switch ($key)
			{

				default:
					$result[$key] = $value;
					break;
			}
		}

		return $result;
	}



	public static function list($_string = null, $_args = [])
	{

		if(!\dash\user::id())
		{
			return false;
		}

		$default_args =
		[
			'order' => null,
			'sort'  => null,
		];

		if(!is_array($_args))
		{
			$_args = [];
		}

		$option = [];
		$option = array_merge($default_args, $_args);

		if($option['order'])
		{
			if(!in_array($option['order'], ['asc', 'desc']))
			{
				unset($option['order']);
			}
		}

		if($option['sort'])
		{
			if(!in_array($option['sort'], self::$sort_field))
			{
				unset($option['sort']);
			}
		}

		$field             = [];

		$result = \dash\db\user_android::search($_string, $option, $field);

		$temp            = [];


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


}
?>