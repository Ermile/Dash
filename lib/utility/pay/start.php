<?php
namespace dash\utility\pay;


class start
{
	public static function bank($_args)
	{
		$default =
		[
			'bank'  => null,
			'token' => null,
		];

		if(!is_array($_args))
		{
			$_args = [];
		}

		$_args = array_merge($default, $_args);

		$token = $_args['token'];
		if(!$token)
		{
			\dash\notif::error(T_("Invalid token"));
			return self::endCompile();
		}

        \dash\utility\pay\setting::load_token($token);

        $bank = mb_strtolower($_args['bank']);
        if(!$bank)
		{
			\dash\notif::error(T_("Please select one of the available ports"));
			return self::endCompile();
		}

		$bank_status = \dash\option::config($bank, 'status');
		if(!$bank_status)
		{
			\dash\notif::error(T_("This bank is disabled on this service"));
			return self::endCompile();
		}

		$banktoken = \dash\utility\pay\setting::get_banktoken();

		if($banktoken)
		{
			$duplicate_record =self::duplicate_record();

			if(!$duplicate_record)
			{
				\dash\notif::error(T_("This record is go to bank"));
				return self::endCompile();
			}
			else
			{
        		\dash\utility\pay\setting::load_token($duplicate_record, true);
			}
		}

        if(is_callable(["\\dash\\utility\\pay\\api\\$bank\\go", "bank"]))
        {
        	\dash\utility\pay\setting::set_payment($bank);
        	\dash\utility\pay\setting::set_title(T_("Pay whit :bank", ['bank' => T_(ucfirst($bank))]));

            ("\\dash\\utility\\pay\\api\\$bank\\go")::bank();
        }
        else
        {
            \dash\notif::error(T_("This payment is not supported in this system"));
        }

        return self::endCompile();
	}


	private static function duplicate_record()
	{
		$detail    = \dash\utility\pay\setting::get_all();

		$payment_response = [];

		if(isset($detail['payment_response']))
		{
			$payment_response = json_decode($detail['payment_response'], true);
		}

		if(isset($payment_response['raw']) && is_array($payment_response['raw']))
		{
			$payment_response['raw']['get_token'] = true;
			$token = self::token($payment_response['raw'], true);
			return $token;
		}
		else
		{
			return false;
		}
	}


	public static function site($_args)
	{
		if(!is_array($_args))
		{
			$_args = [];
		}

		if(!array_key_exists('get_token', $_args))
		{
			$_args['get_token'] = false;
		}

		return self::token($_args);
	}

	public static function token($_args, $_return = false)
	{
		$default =
		[
			'amount'          => 0,
			'bank'            => null,
			'title'           => null,
			'unit'            => null,
			'type'            => null,
			'fromurl'         => null, // from url
			'turn_back'       => null, // back to this url
			'msg_go'          => null,
			'msg_back_ok'     => null,
			'msg_back_failed' => null,
			'auto_go'         => false,
			'auto_back'       => false,
			'final_fn'        => null,
			'final_fn_args'   => null,
			'final_msg'       => false,
			'user_id'         => null,
			'get_token'       => false,
			'other_field'     => [],
		];

		if(!is_array($_args))
		{
			$_args = [];
		}

		$_args = array_merge($default, $_args);

		$user_id = $_args['user_id'];

        if(!$user_id || !is_numeric($user_id))
        {
            if($user_id === 'unverify')
            {
                // pay as undefined user in some project
            }
            else
            {
            	\dash\notif::error(T_("Invalid user"));
                return self::endCompile($_args);
            }
        }

        if(is_numeric($_args['amount']) && $_args['amount'] > 0 && $_args['amount'] == round($_args['amount'], 0))
        {
            // no problem to continue!
        }
        else
        {
            \dash\notif::error(T_("Invalid amount"));
            return self::endCompile($_args);
        }

	    return self::generate_token($_args, $_return);
	}


	private static function endCompile($_args = [])
	{
		if(isset($_args['get_token']) && $_args['get_token'])
		{
			\dash\code::jsonBoom(\dash\notif::get());
		}
		else
		{
			return false;
		}
	}


	private static function generate_token($_args, $_return)
	{
		$payment_response =
		[
			'fromurl'         => $_args['fromurl'],
			'turn_back'       => $_args['turn_back'],
			'msg_go'          => $_args['msg_go'],
			'msg_back_ok'     => $_args['msg_back_ok'],
			'msg_back_failed' => $_args['msg_back_failed'],
			'auto_back'       => $_args['auto_back'],
			'final_fn'        => $_args['final_fn'],
			'final_fn_args'   => $_args['final_fn_args'],
			'final_msg'       => $_args['final_msg'],
			'auto_go'         => $_args['auto_go'],
			'get_token'       => $_args['get_token'],
			'raw'             => $_args,
		];

		$payment_response = json_encode($payment_response, JSON_UNESCAPED_UNICODE);

		if(\dash\utility\filter::is_larger($_args['amount'], 999999999999999999))
		{
			\dash\notif::error(T_("Amount is out of range"), 'amount');
			return false;
		}

		$insert_transaction =
		[
			'caller'           => 'payment',
			'title'            => $_args['title'] ? $_args['title'] : T_("Pay whith :bank",['bank' => T_(ucfirst($_args['bank']))]),
			'unit'             => $_args['unit'] ? $_args['unit'] : 'toman',
			'type'             => $_args['type'] ? $_args['type'] : 'money',
			'plus'             => $_args['amount'],
			'amount_request'   => $_args['amount'],
			'payment'          => mb_strtolower($_args['bank']),
			'user_id'          => $_args['user_id'],
			'other_field'      => $_args['other_field'],
			'payment_response' => $payment_response,
		];

		if(isset($_args['other_field']) && is_array($_args['other_field']))
        {
            $insert_transaction['other_field'] = $_args['other_field'];
        }

		$token = json_encode($insert_transaction);
		$token .= (string) time();
		$token .= (string) rand(1,9999);
		$token .= (string) rand(1,9999);
		$token .= (string) rand(1,9999);
		$token = md5($token);

		$insert_transaction['condition'] = 'request';
		$insert_transaction['token']     = $token;

		$result = \dash\utility\pay\transactions::start($insert_transaction);

		if(!$result)
		{
			return self::endCompile($_args);
		}

		$url = \dash\url::kingdom(). '/pay/'. $token;

		if($_args['get_token'])
		{
			$detail =
			[
				'token' => $token,
				'url'   => $url,
			];

			if($_return)
			{
				return $token;
			}
			else
			{
				\dash\notif::result($detail);
				\dash\code::jsonBoom(\dash\notif::get());
			}
		}
		else
		{
			if(isset($_args['auto_go']) && $_args['auto_go'])
			{
				return self::bank(['token' => $token, 'bank' => self::default_bank()]);
			}
			else
			{
				\dash\redirect::to($url);
			}
		}

	}


	private static function default_bank()
	{
		if(\dash\option::config('default_payment'))
		{
			return \dash\option::config('default_payment');
		}
	}

}
?>