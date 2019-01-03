<?php
namespace dash\utility\pay;


class verify
{
	public static function verify($_bank, $_token)
	{
		\dash\utility\pay\setting::set();

		if(is_callable(["\\dash\\utility\\pay\\api\\$_bank\\back", 'verify']))
		{
			("\\dash\\utility\\pay\\api\\$_bank\\back")::verify($_token);
			return;
		}
	}


	public static function bank_ok($_amount, $_transaction_id)
	{
		\dash\utility\pay\setting::set_condition('ok');

        \dash\utility\pay\setting::set_amount_end($_amount);

        \dash\utility\pay\setting::set_verify(1);

        \dash\utility\pay\setting::set_budget_field();

        \dash\utility\pay\setting::save();

        \dash\utility\pay\transactions::final_verify($_transaction_id);

	}

	public static function bank_error($_condition)
	{
	    \dash\utility\pay\setting::set_condition($_condition);

        \dash\utility\pay\setting::set_verify(0);

        \dash\utility\pay\setting::save();

        return \dash\utility\pay\setting::turn_back();
	}

}
?>