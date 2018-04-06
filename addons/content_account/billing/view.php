<?php
namespace content_account\billing;

class view extends \content_account\main\view
{
	public function config()
	{

		$this->data->amount        = \dash\request::get('amount');
		$this->data->page['title'] = T_("Billing information");
		$this->data->page['desc']  = T_("Check your balance, charge your account, and bill your invoices!");

		if(\dash\user::login())
		{
			$user_unit             = \dash\app\units::find_user_unit(\dash\user::id(), true);
			$user_unit_id          = \dash\app\units::get_id($user_unit);
			$user_unit_id          = (int) $user_unit_id;
			if($user_unit          == 'dollar')
			{
				$user_unit             = '$';
			}
			$this->data->user_unit = T_($user_unit);

			$user_cash_all = \dash\db\transactions::budget(\dash\user::id(), ['unit' => 'all']);
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
		if(!\dash\user::login())
		{
			return;
		}

		$history = $_args->api_callback;

		$this->data->history = $history;

	}

}
?>