<?php
namespace addons\content_api\v1\transaction\tools;


trait transaction_check_args
{
	public function transaction_check_args($_args, &$args, $_log_meta, $_type = 'insert')
	{
		$log_meta = $_log_meta;

		$caller = \lib\utility::request('caller');
		if($caller && mb_strlen($caller) > 20)
		{
			if($_args['save_log']) \lib\db\logs::set('addons:api:trransaction:caller:invalid', $this->user_id, $log_meta);
			if($_args['debug']) \lib\debug::error(T_("Invalid caller"), 'caller', 'arguments');
			return false;
		}

		$title = \lib\utility::request('title');
		if($title && mb_strlen($title) > 50)
		{
			if($_args['save_log']) \lib\db\logs::set('addons:api:trransaction:title:invalid', $this->user_id, $log_meta);
			if($_args['debug']) \lib\debug::error(T_("You must set title less than 50 character"), 'title', 'arguments');
			return false;
		}

		$user_id = \lib\utility::request('user_id');
		$user_id = \lib\utility\shortURL::decode($user_id);
		if(!$user_id)
		{
			if($_args['save_log']) \lib\db\logs::set('addons:api:trransaction:user_id:invalid', $this->user_id, $log_meta);
			if($_args['debug']) \lib\debug::error(T_("Invalid user_id"), 'user_id', 'arguments');
			return false;
		}

		$amount = \lib\utility::request('amount');
		if(!$amount)
		{
			if($_args['save_log']) \lib\db\logs::set('addons:api:trransaction:amount:not:set', $this->user_id, $log_meta);
			if($_args['debug']) \lib\debug::error(T_("Amount not set"), 'amount', 'arguments');
			return false;
		}

		if(!is_numeric($amount))
		{
			if($_args['save_log']) \lib\db\logs::set('addons:api:trransaction:amount:not:a:number', $this->user_id, $log_meta);
			if($_args['debug']) \lib\debug::error(T_("Amount must be number"), 'amount', 'arguments');
			return false;
		}

		$action = \lib\utility::request('action');
		if(!$action)
		{
			if($_args['save_log']) \lib\db\logs::set('addons:api:trransaction:action:not:set', $this->user_id, $log_meta);
			if($_args['debug']) \lib\debug::error(T_("Action not set"), 'action', 'arguments');
			return false;
		}

		if($action && !in_array($action, ['minus', 'plus']))
		{
			if($_args['save_log']) \lib\db\logs::set('addons:api:trransaction:action:invalid', $this->user_id, $log_meta);
			if($_args['debug']) \lib\debug::error(T_("Invalid action"), 'action', 'arguments');
			return false;
		}

		$type = \lib\utility::request('type');
		if(!$type)
		{
			if($_args['save_log']) \lib\db\logs::set('addons:api:trranstype:type:not:set', $this->user_id, $log_meta);
			if($_args['debug']) \lib\debug::error(T_("Type not set"), 'type', 'arguments');
			return false;
		}

		if($type && !in_array($type, ['gift','prize','transfer','promo','money']))
		{
			if($_args['save_log']) \lib\db\logs::set('addons:api:trranstype:type:invalid', $this->user_id, $log_meta);
			if($_args['debug']) \lib\debug::error(T_("Invalid type"), 'type', 'arguments');
			return false;
		}

		$unit = \lib\utility::request('unit');
		if(!$unit)
		{
			if($_args['save_log']) \lib\db\logs::set('addons:api:trransaction:unit:not:set', $this->user_id, $log_meta);
			if($_args['debug']) \lib\debug::error(T_("Unit not set"), 'unit', 'arguments');
			return false;
		}

		if($unit && !\lib\utility\units::check($unit))
		{
			if($_args['save_log']) \lib\db\logs::set('addons:api:trransaction:unit:invalid', $this->user_id, $log_meta);
			if($_args['debug']) \lib\debug::error(T_("Invalid unit"), 'unit', 'arguments');
			return false;
		}


        $args['caller'] = $caller;
        $args['title'] = $title;
        $args['user_id'] = $user_id;
        if($action === 'plus')
        {
        	$args['plus'] = $amount;
        }
        elseif ($action === 'minus')
        {
        	$args['minus'] = $amount;
        }

        // $args['payment'] = $payment;
        $args['verify'] = 1;
        $args['type'] = $type;
        $args['unit'] = $unit;
        $args['date'] = date("Y-m-d H:i:s");
        // $args['amount_request'] = $amount_request;


	}



	/**
	 * check args and make where
	 *
	 * @param      <type>  $_args      The arguments
	 * @param      <type>  $where      The where
	 * @param      <type>  $_log_meta  The log meta
	 */
	public function transaction_make_where($_args, &$where, $_log_meta)
	{
		$type = \lib\utility::request('type');
		if($type && is_string($type) || is_numeric($type))
		{
			$where['type'] = $type;
		}

		if(!$type && \lib\utility::isset_request('type'))
		{
			$where['type'] = null;
		}
	}
}
?>