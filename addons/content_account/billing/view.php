<?php
namespace content_account\billing;

class view extends \content_account\main\view
{
	public function config()
	{

		$this->data->amount        = \lib\request::get('amount');
		$this->data->page['title'] = T_("Billing information");
		$this->data->page['desc']  = T_("Check your balance, charge your account, and bill your invoices!");

		if($this->login())
		{
			$user_unit             = \lib\utility\units::find_user_unit($this->login('id'), true);
			$user_unit_id          = \lib\utility\units::get_id($user_unit);
			$user_unit_id          = (int) $user_unit_id;
			if($user_unit          == 'dollar')
			{
				$user_unit             = '$';
			}
			$this->data->user_unit = T_($user_unit);

			$user_cash_all = \lib\db\transactions::budget($this->login('id'), ['unit' => 'all']);
			$this->data->user_cash_all = $user_cash_all;
			if(is_array($user_cash_all))
			{
				$user_cash_all['total']    = array_sum($user_cash_all);
			}
			$this->data->user_cash = $user_cash_all;

		}
	}



	/**
	 * { function_description }
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function view_billing($_args)
	{
		if(!$this->login())
		{
			return;
		}

		$history = $_args->api_callback;

		$this->data->history = $history;

	}

}
?>