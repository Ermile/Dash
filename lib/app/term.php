<?php
namespace lib\app;

/**
 * Class for user.
 */
class term
{
	public static $sort_field =
	[
		'title',
		'slug',
	];

	/**
	 * check args
	 *
	 * @return     array|boolean  ( description_of_the_return_value )
	 */
	public static function check($_option = [])
	{
		$default_option =
		[
			'save_log' => true,
			'debug'    => true,
		];

		if(!is_array($_option))
		{
			$_option = [];
		}

		$_option = array_merge($default_option, $_option);

		$title = \lib\app::request('title');
		if(!$title)
		{
			\lib\debug::error(T_("Please set the term title"), 'title');
			return false;
		}

		if(mb_strlen($title) > 150)
		{
			\lib\debug::error(T_("Please set the term title less than 150 character"), 'title');
			return false;
		}


		$slug = \lib\app::request('slug');

		if($slug && mb_strlen($slug) > 150)
		{
			\lib\debug::error(T_("Please set the term slug less than 150 character"), 'slug');
			return false;
		}

		$desc = \lib\app::request('desc');
		if($desc && mb_strlen($desc) > 500)
		{
			\lib\debug::error(T_("Please set the term desc less than 500 character"), 'desc');
			return false;
		}


		$status = \lib\app::request('status');

		if($status && !in_array($status, ['enable', 'disable']))
		{
			\lib\debug::error(T_("Invalid status of term"));
			return false;
		}

		$args           = [];
		$args['title']  = $title;
		$args['desc']   = $desc;
		$args['status'] = $status;
		$args['slug']   = $slug;

		return $args;
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
				case 'id':
				case 'parent':
					if(isset($value))
					{
						$result[$key] = \lib\utility\shortURL::encode($value);
					}
					else
					{
						$result[$key] = null;
					}
					break;

				default:
					$result[$key] = $value;
					break;
			}
		}

		return $result;
	}


	/**
	 * add new user
	 *
	 * @param      array          $_args  The arguments
	 *
	 * @return     array|boolean  ( description_of_the_return_value )
	 */
	public static function add($_args, $_option = [])
	{
		\lib\app::variable($_args);


		$default_option =
		[
			'debug'    => true,
		];

		if(!is_array($_option))
		{
			$_option = [];
		}

		$_option = array_merge($default_option, $_option);


		if(!\lib\user::id())
		{
			if($_option['debug']) \lib\debug::error(T_("User not found"), 'user');
			return false;
		}

		// check args
		$args = self::check($_option);

		if($args === false || !\lib\debug::$status)
		{
			return false;
		}

		$return         = [];

		$term_id = \lib\db\terms::insert($args);

		if(!$term_id)
		{
			\lib\debug::error(T_("No way to insert term"));
			return false;
		}

		return $return;
	}

	public static function list($_string = null, $_args = [])
	{

		if(!\lib\user::id())
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

		unset($option['in']);

		$result = \lib\db\terms::search($_string, $option, $field);

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


	public static function edit($_args, $_option = [])
	{
		\lib\app::variable($_args);

		$default_option =
		[

		];

		if(!is_array($_option))
		{
			$_option = [];
		}

		$_option = array_merge($default_option, $_option);


		// check args
		$args = self::check($_option);

		if($args === false || !\lib\debug::$status)
		{
			return false;
		}

		$id = \lib\app::request('id');
		$id = \lib\utility\shortURL::decode($id);

		if(!$id)
		{
			\lib\debug::error(T_("Can not access to edit term"), 'term');
			return false;
		}

		\lib\db\terms::update($args, $id);

		return true;
	}
}
?>