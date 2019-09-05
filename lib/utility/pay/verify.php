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
		if(\dash\utility\pay\setting::get_condition() === 'pending')
		{
			\dash\utility\pay\setting::set_condition('ok');

	        \dash\utility\pay\setting::set_amount_end($_amount);

	        \dash\utility\pay\setting::set_verify(1);

	        \dash\utility\pay\setting::set_budget_field();

	        \dash\utility\pay\setting::save();

	        self::call_final_fn();

	        // \dash\utility\pay\transactions::final_verify($_transaction_id);
		}
		else
		{
			// can not verify again
			\dash\header::status(403, T_("Dont!"));
		}

	}


	private static function call_final_fn()
	{
		$detail = \dash\utility\pay\setting::get_payment_response();

		if(is_string($detail))
		{
			$detail = json_decode($detail, true);
		}

		if(isset($detail['final_fn']) && is_array($detail['final_fn']) && isset($detail['final_fn'][0]) && isset($detail['final_fn'][1]))
		{
			$namespace = $detail['final_fn'][0];
			$namespace = str_replace('/', '\\', $namespace);

			$fn        = $detail['final_fn'][1];

			if(is_callable([$namespace, $fn]))
			{
				if(isset($detail['final_fn_args']))
				{
					$namespace::$fn($detail['final_fn_args'], \dash\utility\pay\setting::get_all());
				}
				else
				{
					$namespace::$fn(\dash\utility\pay\setting::get_all());
				}
			}
		}
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