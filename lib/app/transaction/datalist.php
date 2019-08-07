<?php
namespace dash\app\transaction;

trait datalist
{

	public static $sort_field =
	[
		'id',
		'verify',
		'user_id',
		'type',
		'budget',
		'budget_before',
		'title',
		'dateverify',
		'status',
		'condition',
		'payment',
		'plus',
		'minus',
		'datecreated',
	];


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

		$option             = [];
		$option             = array_merge($default_args, $_args);

		if($option['order'])
		{
			if(!in_array($option['order'], ['asc', 'desc']))
			{
				$option['order'] = 'desc';
			}
		}
		else
		{
			$option['order'] = 'desc';
		}



		if($option['sort'])
		{
			if(!in_array($option['sort'], self::$sort_field))
			{
				unset($option['sort']);
			}
		}

		$field             = [];

		unset($option['in']);

		$result = \dash\db\transactions::search($_string, $option, $field);
		if(is_array($result))
		{
			$result = array_map(['\\dash\\app', 'ready'], $result);
		}
		return $result;
	}

}

?>