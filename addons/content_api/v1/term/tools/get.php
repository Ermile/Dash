<?php
namespace addons\content_api\v1\term\tools;


trait get
{

	/**
	 * Gets the term.
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  The term.
	 */
	public function get_list_term($_args = [])
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

		$get_args = $this->term_make_where($_args, $where, $log_meta);

		if(!\lib\debug::$status || $get_args === false)
		{
			return false;
		}

		$result          = \lib\db\terms::search($search, $where);

		$temp            = [];

		if(is_array($result))
		{
			foreach ($result as $key => $value)
			{
				$check = $this->ready_term($value);
				if($check)
				{
					$temp[] = $check;
				}
			}
		}
		return $temp;
	}


	/**
	 * Gets the term.
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  The term.
	 */
	public function get_term($_args = [])
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
			\lib\db\logs::set('api:term:term_id:notfound', $this->user_id, $log_meta);
			\lib\debug::error(T_("term not found"), 'term', 'permission');
			return false;
		}


		$id = \lib\utility::request('id');
		$id = \lib\utility\shortURL::decode($id);

		if(!$id)
		{
			\lib\db\logs::set('api:term:id:not:set', $this->user_id, $log_meta);
			\lib\debug::error(T_("Id not set"), 'id', 'arguments');
			return false;
		}

		$get_term = \lib\db\terms::get(['id' => $id, 'limit' => 1]);

		$result = $this->ready_term($get_term);

		return $result;
	}



	/**
	 * ready data of term to load in api result
	 *
	 * @param      <type>  $_data     The data
	 * @param      array   $_options  The options
	 *
	 * @return     array   ( description_of_the_return_value )
	 */
	public function ready_term($_data, $_options = [])
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
				case 'user_id':
				case 'parent':
					$result[$key] = \lib\utility\shortURL::encode($value);
					break;

				case 'meta':
				case 'date_modified':
				case 'usercount':
				case 'createdate':
				case 'count':
				case 'caller':
					continue;
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