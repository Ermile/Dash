<?php
namespace dash\app;

class comment
{

	public static $sort_field =
	[
		'id',
	];

	public static function get($_id)
	{
		$id = \dash\coding::decode($_id);
		if(!$id)
		{
			return false;
		}
		$get = \dash\db\comments::get(['id' => $id, 'limit' => 1]);
		if(is_array($get))
		{
			return self::ready($get);
		}
		return false;
	}

	public static function edit($_args, $_id)
	{
		\dash\app::variable($_args);
		// check args
		$id = \dash\coding::decode($_id);
		if(!$id)
		{
			\dash\notif::error(T_("Can not access to edit comment"));
			return false;
		}

		$args = self::check($id);

		if($args === false || !\dash\engine\process::status())
		{
			return false;
		}


		\dash\db\comments::update($args, $id);

		if(\dash\engine\process::status())
		{
			\dash\notif::ok(T_("Comment successfully updated"));
		}
	}

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

		if($_args['sort'] && !in_array($_args['sort'], self::$sort_field))
		{
			$_args['sort'] = null;
		}

		$_args = array_merge($default_meta, $_args);

		$result            = \dash\db\comments::search($_string, $_args);
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


	public static function check($_id = null, $_option = [])
	{

		$default_option =
		[
			'meta' => [],
		];

		if(!is_array($_option))
		{
			$_option = [];
		}

		$_option = array_merge($default_option, $_option);

		$status = \dash\app::request('status');
		if($status && !in_array($status, ['approved', 'unapproved', 'spam', 'deleted', 'awaiting']))
		{
			\dash\notif::error(T_("Invalid status"), 'status');
			return false;
		}


		$args           = [];
		$args['status'] = $status;
		return $args;
	}


	/**
	 * ready data of classroom to load in api
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
				case 'user_id':
				case 'parent':
				case 'term_id':
					if(isset($value))
					{
						$result[$key] = \dash\coding::encode($value);
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
}
?>