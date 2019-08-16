<?php
namespace content_account\billing;

class view
{
	public static function config()
	{

		\dash\data::amount(\dash\request::get('amount'));
		\dash\data::page_title(T_("Billing information"));
		\dash\data::page_desc(T_("Check your balance, charge your account, and bill your invoices!"));

		\dash\data::badge_link(\dash\url::here());
		\dash\data::badge_text(T_('Back to Account'));


		if(\dash\user::login())
		{
			$user_unit    = \dash\app\units::find_user_unit(\dash\user::id(), true);
			$user_unit_id = \dash\app\units::get_id($user_unit);
			$user_unit_id = (int) $user_unit_id;

			if($user_unit == 'dollar')
			{
				$user_unit             = '$';
			}

			\dash\data::userUnit(T_($user_unit));

			$user_cash_all = \dash\db\transactions::budget(\dash\user::id(), ['unit' => 'all']);

			// if(is_array($user_cash_all))
			// {
			// 	$user_cash_all['total']    = array_sum($user_cash_all);
			// }
			\dash\data::userCash($user_cash_all);

			\dash\data::history(\content_account\billing\model::get_billing());
		}
	}



}
?>