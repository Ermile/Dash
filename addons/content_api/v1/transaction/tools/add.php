<?php
namespace addons\content_api\v1\transaction\tools;


trait add
{

	use transaction_check_args;
	/**
	 * Adds a transaction.
	 *
	 * @param      array    $_args  The arguments
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public function add_transaction($_args = [])
	{

		// ready to insert transactionteam or transactionbranch record
		$args                  = [];

		// default args
		$default_args =
		[
			'method'   => 'post',
			'debug'    => true,
			'save_log' => true,
		];

		if(!is_array($_args))
		{
			$_args = [];
		}
		// merge default args and args
		$_args = array_merge($default_args, $_args);

		// set default title of debug
		// if($_args['debug']) \lib\notif::title(T_("Operation Faild"));

		// set the log meta
		$log_meta =
		[
			'data' => null,
			'meta' =>
			[
				'user_id' => $this->user_id,
				'input'   => \lib\utility::request(),
			]
		];

		// check transaction id is exist
		if(!$this->user_id)
		{
			if($_args['save_log']) \lib\db\logs::set('addon:api:transaction:user_id:notfound', $this->user_id, $log_meta);
			if($_args['debug']) \lib\notif::error(T_("transaction not found"), 'transaction', 'permission');
			return false;
		}


		/**
		 * check and set the args
		 */
		$return_function = $this->transaction_check_args($_args, $args, $log_meta);

		if(!\lib\notif::$status || $return_function === false)
		{
			return false;
		}

		// insert new transaction team
		if($_args['method'] === 'post')
		{
			\lib\db\transactions::set($args);
		}
		elseif($_args['method'] === 'patch')
		{

			// $id = \lib\utility::request('id');
			// $id = \lib\utility\shortURL::decode($id);
			// if(!$id)
			// {
			// 	if($_args['save_log']) \lib\db\logs::set('addon:api:transaction:pathc:id:not:set', $this->user_id, $log_meta);
			// 	if($_args['debug']) \lib\notif::error(T_("Id not set"), 'id', 'arguments');
			// 	return false;
			// }

			// if(!empty($args))
			// {
			// 	\lib\db\transactions::update($args, $id);
			// }
		}

		if(\lib\notif::$status)
		{
			// if($_args['debug']) \lib\notif::title(T_("Operation Complete"));

			if($_args['method'] === 'post')
			{
				if($_args['debug']) \lib\notif::true(T_("transaction successfully added"));
			}
			elseif($_args['method'] === 'patch')
			{
				if($_args['debug']) \lib\notif::true(T_("transaction successfully updated"));
			}
		}
	}
}
?>