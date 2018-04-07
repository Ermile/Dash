<?php
namespace content_api\v1\invoice\tools;


trait get
{

	public $remote_invoice         = false;
	public $rule                = null;
	public $show_another_status = false;
	public $team_privacy        = 'private';

	/**
	 * Gets the invoice.
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  The invoice.
	 */
	public function get_list_invoice($_args = [])
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
				'input' => \dash\utility::request(),
			]
		];

		if(!$this->invoice_id)
		{
			return false;
		}
		$where           = [];
		$search          = \dash\utility::request('search');

		$get_args = $this->invoice_make_where($_args, $where, $log_meta);

		if(!\dash\engine\process::status() || $get_args === false)
		{
			return false;
		}

		$result          = \dash\db\invoices::search($search, $where);

		$temp            = [];

		if(is_array($result))
		{
			foreach ($result as $key => $value)
			{
				$check = $this->ready_invoice($value);
				if($check)
				{
					$temp[] = $check;
				}
			}
		}
		return $temp;
	}


	/**
	 * Gets the invoice.
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  The invoice.
	 */
	public function get_invoice($_args = [])
	{
		// \dash\notif::title(T_("Operation Faild"));

		$log_meta =
		[
			'data' => null,
			'meta' =>
			[
				'input' => \dash\utility::request(),
			]
		];

		if(!$this->invoice_id)
		{
			\dash\db\logs::set('api:invoice:invoice_id:notfound', $this->invoice_id, $log_meta);
			\dash\notif::error(T_("invoice not found"), 'invoice', 'permission');
			return false;
		}


		$id = \dash\utility::request('id');
		$id = \dash\coding::decode($id);
		if(!$id)
		{
			\dash\db\logs::set('api:invoice:id:not:set', $this->invoice_id, $log_meta);
			\dash\notif::error(T_("Id not set"), 'id', 'arguments');
			return false;
		}

		$get_invoice = \dash\db\invoices::get(['id' => $id, 'limit' => 1]);

		$result = $this->ready_invoice($get_invoice);

		return $result;
	}



	/**
	 * ready data of invoice to load in api result
	 *
	 * @param      <type>  $_data     The data
	 * @param      array   $_options  The options
	 *
	 * @return     array   ( description_of_the_return_value )
	 */
	public function ready_invoice($_data, $_options = [])
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
					$result[$key] = \dash\coding::encode($value);
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