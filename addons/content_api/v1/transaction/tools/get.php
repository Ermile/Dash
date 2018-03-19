<?php
namespace addons\content_api\v1\transaction\tools;


trait get
{

	public $remote_transaction         = false;
	public $rule                = null;
	public $show_another_status = false;
	public $team_privacy        = 'private';

	/**
	 * Gets the transaction.
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  The transaction.
	 */
	public function get_list_transaction($_args = [])
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

		if(!$this->transaction_id)
		{
			return false;
		}
		$where           = [];
		$search          = \lib\utility::request('search');

		$get_args = $this->transaction_make_where($_args, $where, $log_meta);

		if(!\lib\engine\process::status() || $get_args === false)
		{
			return false;
		}

		$result          = \lib\db\transactions::search($search, $where);

		$temp            = [];

		if(is_array($result))
		{
			foreach ($result as $key => $value)
			{
				$check = $this->ready_transaction($value);
				if($check)
				{
					$temp[] = $check;
				}
			}
		}
		return $temp;
	}


	/**
	 * Gets the transaction.
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  The transaction.
	 */
	public function get_transaction($_args = [])
	{
		\lib\notif::title(T_("Operation Faild"));

		$log_meta =
		[
			'data' => null,
			'meta' =>
			[
				'input' => \lib\utility::request(),
			]
		];

		if(!$this->transaction_id)
		{
			\lib\db\logs::set('api:transaction:transaction_id:notfound', $this->transaction_id, $log_meta);
			\lib\notif::error(T_("transaction not found"), 'transaction', 'permission');
			return false;
		}


		$id = \lib\utility::request('id');
		$id = \lib\coding::decode($id);
		if(!$id)
		{
			\lib\db\logs::set('api:transaction:id:not:set', $this->transaction_id, $log_meta);
			\lib\notif::error(T_("Id not set"), 'id', 'arguments');
			return false;
		}

		$get_transaction = \lib\db\transactions::get(['id' => $id, 'limit' => 1]);

		$result = $this->ready_transaction($get_transaction);

		return $result;
	}



	/**
	 * ready data of transaction to load in api result
	 *
	 * @param      <type>  $_data     The data
	 * @param      array   $_options  The options
	 *
	 * @return     array   ( description_of_the_return_value )
	 */
	public function ready_transaction($_data, $_options = [])
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
					$result[$key] = \lib\coding::encode($value);
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