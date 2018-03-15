<?php
namespace addons\content_api\v1\user\tools;


trait get
{

	public $remote_user         = false;
	public $rule                = null;
	public $show_another_status = false;
	public $team_privacy        = 'private';

	/**
	 * Gets the user.
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  The user.
	 */
	public function get_list_user($_args = [])
	{
		$default_args =

		[
			'pagenation' => true,
			'admin'  	 => false,
		];

		if(!is_array($_args))
		{
			$_args = [];
		}

		$_args = array_merge($default_args, $_args);

		$log_meta =
		[
			'data' => null,
			'meta' =>
			[
				'input' => \lib\utility::request(),
			]
		];

		if(!$this->user_id)
		{
			return false;
		}
		$where           = [];
		$search          = \lib\utility::request('search');

		$get_args = $this->user_make_where($_args, $where, $log_meta);

		if(!\lib\debug::$status || $get_args === false)
		{
			return false;
		}

		$result          = \lib\db\users::search($search, $where);

		$temp            = [];

		if(is_array($result))
		{
			foreach ($result as $key => $value)
			{
				$check = $this->ready_user($value);
				if($check)
				{
					$temp[] = $check;
				}
			}
		}


		if(\lib\utility::request('get_term'))
		{
			$this->get_user_term($temp);
		}

		return $temp;
	}


	/**
	 * Gets the user.
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  The user.
	 */
	public function get_user($_args = [])
	{
		\lib\debug::title(T_("Operation Faild"));

		$log_meta =
		[
			'data' => null,
			'meta' =>
			[
				'input' => \lib\utility::request(),
			]
		];

		if(!$this->user_id)
		{
			\lib\db\logs::set('api:user:user_id:notfound', $this->user_id, $log_meta);
			\lib\debug::error(T_("User not found"), 'user', 'permission');
			return false;
		}


		$id = \lib\utility::request('id');
		$id = \lib\utility\shortURL::decode($id);
		if(!$id)
		{
			\lib\db\logs::set('api:user:id:not:set', $this->user_id, $log_meta);
			\lib\debug::error(T_("Id not set"), 'id', 'arguments');
			return false;
		}

		$get_user = \lib\db\users::get(['id' => $id, 'limit' => 1]);

		$result = $this->ready_user($get_user);

		if(\lib\utility::request('get_term'))
		{
			$this->get_user_term($result);
		}

		return $result;
	}


	public function get_user_term(&$_data)
	{
		$multi = false;
		if(array_key_exists(0, $_data))
		{
			$multi = true;
		}

		$user_ids = [];

		if(!$multi)
		{
			if(isset($_data['id']))
			{
				$user_ids = [$_data['id']];
			}
		}
		else
		{
			$user_ids = array_column($_data, 'id');
		}

		$user_ids_decode = array_map(function($_a){return \lib\utility\shortURL::decode($_a);}, $user_ids);

		$get_term_multi =
		[
			'related_id' => ["IN", "(". implode(',', $user_ids_decode).")"],
			'related'    => 'users',
			'status'     => 'enable',
		];

		$cat_tag = [];

		$get_term_multi = \lib\db\termusages::get($get_term_multi);

		if(is_array($get_term_multi))
		{
			foreach ($get_term_multi as $key => $value)
			{
				if(isset($value['related_id']) && isset($value['type']) && isset($value['title']))
				{
					$related_encode = \lib\utility\shortURL::encode($value['related_id']);
					$cat_tag[$related_encode][$value['type']][] = $value['title'];
				}
			}
		}


		if(!empty($cat_tag))
		{
			if($multi)
			{
				foreach ($_data as $key => $value)
				{
					if(isset($value['id']))
					{
						if(array_key_exists($value['id'], $cat_tag))
						{
							$_data[$key]['term'] = $cat_tag[$value['id']];
						}
					}
				}
			}
			else
			{
				if(isset($_data['id']) && isset($cat_tag[$_data['id']]))
				{
					$_data['term'] = $cat_tag[$_data['id']];
				}
			}
		}

	}


	/**
	 * ready data of user to load in api result
	 *
	 * @param      <type>  $_data     The data
	 * @param      array   $_options  The options
	 *
	 * @return     array   ( description_of_the_return_value )
	 */
	public function ready_user($_data, $_options = [])
	{
		$default_options =
		[

		];

		if(!is_array($_options))
		{
			$_options = [];
		}

		$_options = array_merge($default_options, $_options);


		$result = [];

		foreach ($_data as $key => $value)
		{
			switch ($key)
			{
				case 'id':
				case 'fileid':
				case 'parent':
					$result[$key] = \lib\utility\shortURL::encode($value);
					break;

				case 'birthday':
					$result['birthday']           = $value;
					$result['birthday_gregorian'] = $value;
					if($value)
					{
						if(strtotime($value) !== false)
						{
							$time = strtotime($value);
							$toGregorian = \lib\utility\jdate::toGregorian(date("Y", $time), date("m", $time), date("d", $time));
							$result['birthday_gregorian'] = implode('-', $toGregorian);
						}
					}
					break;
				case 'fileurl':
					if($value)
					{
						$value = $this->host('file'). '/'. $value;
					}
					$result[$key] = $value;
					break;
				case 'meta':
					if(is_string($value) && substr($value, 0, 1) === '{')
					{
						$value = json_decode($value, true);
					}
					$result[$key] = $value;
					break;

				default:
					$result[$key] = $value;
					break;
			}

		}

		krsort($result);
		return $result;
	}
}
?>