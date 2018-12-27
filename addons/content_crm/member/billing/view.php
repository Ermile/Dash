<?php
namespace content_crm\member\billing;

class view
{
	public static function config()
	{
		\dash\data::page_title(T_("Billing information"));
		\dash\data::page_desc(T_("Billing"));

		\content_crm\member\main\view::dataRowMember();

		$user_id = \dash\coding::decode(\dash\request::get('id'));

		$user_unit    = \dash\app\units::find_user_unit($user_id, true);
		$user_unit_id = \dash\app\units::get_id($user_unit);
		$user_unit_id = (int) $user_unit_id;

		if($user_unit == 'dollar')
		{
			$user_unit             = '$';
		}

		\dash\data::userUnit(T_($user_unit));

		$user_cash_all = \dash\db\transactions::budget($user_id, ['unit' => 'all']);

		\dash\data::userCash($user_cash_all);


		$meta            = [];
		$meta['user_id'] = $user_id;
		$meta['verify']  = 1;
		$billing_history = \dash\db\transactions::search(null, $meta);
		\dash\data::history($billing_history);
	}
}
?>