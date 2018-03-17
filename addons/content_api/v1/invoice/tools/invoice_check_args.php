<?php
namespace addons\content_api\v1\invoice\tools;


trait invoice_check_args
{
	public function invoice_check_args($_args, &$args, $_log_meta, $_type = 'insert')
	{
		$log_meta = $_log_meta;

		$title  = \lib\utility::request('title');
		if(!$title)
		{
			if($_args['save_log']) \lib\db\logs::set('addons:api:invoice:title:not:set', $this->user_id, $log_meta);
			if($_args['debug']) \lib\notif::error(T_("title id not found"), 'title', 'arguments');
			return false;
		}

		if($title && mb_strlen($title) > 50)
		{
			if($_args['save_log']) \lib\db\logs::set('addons:api:invoice:title:max:length', $this->user_id, $log_meta);
			if($_args['debug']) \lib\notif::error(T_("You must set title less than 50 character"), 'title', 'arguments');
			return false;
		}

		$buyer  = \lib\utility::request('buyer');
		$buyer = \lib\utility\filter::mobile($buyer);
		if(!$buyer)
		{
			if($_args['save_log']) \lib\db\logs::set('addons:api:invoice:buyer:not:set', $this->user_id, $log_meta);
			if($_args['debug']) \lib\notif::error(T_("buyer id not found"), 'buyer', 'arguments');
			return false;
		}

		$buyer_detail = \lib\db\users::get(['mobile' => $buyer, 'limit' => 1]);
		if(isset($buyer_detail['id']))
		{
			$buyer_id = $buyer_detail['id'];
		}
		else
		{
			if($_args['save_log']) \lib\db\logs::set('addons:api:invoice:buyer:not:found', $this->user_id, $log_meta);
			if($_args['debug']) \lib\notif::error(T_("buyer not found"), 'buyer', 'arguments');
			return false;
		}

		// $seller = \lib\utility::request('seller');
		// no now !
		$seller = null;

		// if(!$seller)
		// {

			// if($_args['save_log']) \lib\db\logs::set('addons:api:invoice:seller:not:set', $this->user_id, $log_meta);
			// if($_args['debug']) \lib\notif::error(T_("seller id not found"), 'seller', 'arguments');
			// return false;
		// }

		$desc  = \lib\utility::request('desc');
		if($desc && mb_strlen($desc) > 50)
		{
			if($_args['save_log']) \lib\db\logs::set('addons:api:invoice:desc:max:length', $this->user_id, $log_meta);
			if($_args['debug']) \lib\notif::error(T_("You must set desc less than 50 character"), 'desc', 'arguments');
			return false;
		}


		$status = \lib\utility::request('status');
		if($status && !in_array($status, ['enable', 'disable', 'expire']))
		{
			if($_args['save_log']) \lib\db\logs::set('addons:api:invoice:status:not:set', $this->user_id, $log_meta);
			if($_args['debug']) \lib\notif::error(T_("Invalid status"), 'status', 'arguments');
			return false;
		}


		$date = \lib\utility::request('date');
		if(strtotime($date) === false)
		{
			if($_args['save_log']) \lib\db\logs::set('addons:api:invoice:date:not:set', $this->user_id, $log_meta);
			if($_args['debug']) \lib\notif::error(T_("Invalid invoice date"), 'date', 'arguments');
			return false;
		}
		$date = date("Y-m-d", strtotime($date));

		if(\lib\utility::isset_request('temp'))
		{
			if(\lib\utility::request('temp'))
			{
				$temp_invoice = 1;
			}
			else
			{
				$temp_invoice = 0;
			}
		}
		else
		{
			$temp_invoice = null;
		}

		$details = \lib\utility::request('details');
		if(!$details)
		{
			if($_args['save_log']) \lib\db\logs::set('addon:api:invoice:detail:not:found', $this->user_id, $log_meta);
			if($_args['debug']) \lib\notif::error(T_("No detail was sended"), 'detail', 'arguments');
			return false;
		}

		if(!is_array($details))
		{
			if($_args['save_log']) \lib\db\logs::set('addon:api:invoice:details:not:array', $this->user_id, $log_meta);
			if($_args['debug']) \lib\notif::error(T_("You must set the details as array"), 'details', 'arguments');
			return false;
		}


		$temp_detail    = [];
		$total_price    = 0;
		$count_detail   = 0;
		$total_discount = 0;

		foreach ($details as $key => $value)
		{
			if
			(
				array_key_exists('title', $value) &&
				array_key_exists('price', $value) &&
				array_key_exists('count', $value) &&
				is_numeric($value['price']) &&
				is_numeric($value['count'])
			)
			{
				$count_detail++;

				$total = (floatval($value['price']) * intval($value['count']));

				$discount = null;

				if(isset($value['discount']))
				{

					$discount = floatval($value['discount']);
					$total_discount += $discount;
					$total -= $discount;
				}

				$total_price += $total;

				$temp =
				[
					'title'    => $value['title'],
					'price'    => $value['price'],
					'count'    => $value['count'],
					'total'    => $total,
					'discount' => $discount,
				];

				$temp_detail[] = $temp;
			}
		}

		if(!$count_detail)
		{
			if($_args['save_log']) \lib\db\logs::set('addon:api:invoice:details:invalid', $this->user_id, $log_meta);
			if($_args['debug']) \lib\notif::error(T_("No valid details was sended"), 'details', 'arguments');
			return false;
		}

		$args['title']          = $title;
		$args['user_id']        = $buyer_id;
		$args['user_id_seller'] = $seller;
		$args['status']         = $status ? $status : 'enable';
		$args['date']           = $date;
		$args['desc']           = $desc;
		$args['temp']           = $temp_invoice;
		// use in trait add and unset
		// not set in sql
		$args['details']        = $temp_detail;

		if($total_price)
		{
			$args['total']      = $total_price;
		}

		if($count_detail)
		{
			$args['count_detail'] = $count_detail;
		}

		if($total_discount)
		{
			$args['total_discount'] = $total_discount;
		}
	}



	/**
	 * check args and make where
	 *
	 * @param      <type>  $_args      The arguments
	 * @param      <type>  $where      The where
	 * @param      <type>  $_log_meta  The log meta
	 */
	public function invoice_make_where($_args, &$where, $_log_meta)
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