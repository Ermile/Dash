<?php
namespace dash\utility\pay;


class setting
{

	private  static $load               = false;
	private  static $transaction_detail = [];
	private  static $transaction_update = [];

	public static $default_callback_url = 'hook/pay/verify';


    public static function set()
    {
        // set default timeout for socket
        ini_set("default_socket_timeout", 10);
        ini_set('soap.wsdl_cache_enabled',0);
        ini_set('soap.wsdl_cache_ttl',0);
    }


    public static function get_callbck_url($_payment)
    {
        $callback_url =  \dash\url::base();

        if($_payment === 'redirect_page')
        {
            $callback_url .= '/hook/pay/redirect';
        }
        else
        {
            $callback_url .= '/'. self::$default_callback_url;
            $callback_url .= '/'. $_payment;
            $callback_url .= '/'. self::get_token();
        }

        return $callback_url;
    }


    public static function load_token($_token, $_force = false)
	{
		if(!self::$load || $_force)
		{
			self::$load = true;
			self::$transaction_detail = \dash\utility\pay\transactions::load($_token);
		}
		return self::$transaction_detail;
	}


	public static function load_banktoken($_token, $_banktoken, $_bank)
	{
		if(!self::$load)
		{
			self::$load = true;
			self::$transaction_detail = \dash\utility\pay\transactions::load_banktoken($_token, $_banktoken, $_bank);
		}
		return self::$transaction_detail;
	}


	public static function turn_back()
	{
		if(!\dash\engine\process::status())
		{
			return false;
		}

		$paymentDetail = self::get_field('payment_response');
		if(is_string($paymentDetail))
		{
			$paymentDetail = json_decode($paymentDetail, true);
		}

		$back_url = \dash\url::kingdom();

		if(isset($paymentDetail['turn_back']))
		{
			$back_url = $paymentDetail['turn_back'];
		}

		\dash\redirect::to($back_url);
	}


	public static function __callStatic($_fn, $_arg)
	{
		if(substr($_fn, 0, 4) === 'get_')
		{
			$field = substr($_fn, 4);
			return self::get_field($field);
		}
		elseif(substr($_fn, 0, 4) === 'set_')
		{
			$field = substr($_fn, 4);
			return self::set_field($field, ...$_arg);
		}

	}


	public static function set_budget_field()
	{
		$detail = self::get_all();
		$fields = \dash\utility\pay\transactions::calc_budget($detail);

		if(is_array($fields))
		{
			foreach ($fields as $key => $value)
			{
				self::set_field($key, $value);
			}
		}
	}


	public static function save()
	{
		if(!empty(self::$transaction_update) && self::get_id())
		{

			$result                   = \dash\utility\pay\transactions::update(self::$transaction_update, self::get_id());
			self::$transaction_update = [];
			return $result;
		}
		return null;
	}


	public static function get_all()
	{
		return self::$transaction_detail;
	}


	private static function get_field($_field = null)
	{
		if($_field)
		{
			if(array_key_exists($_field, self::$transaction_detail))
			{
				return self::$transaction_detail[$_field];
			}
			else
			{
				return null;
			}
		}
		else
		{
			return self::$transaction_detail;
		}
	}


	private static function set_field($_field, $_data)
	{
		if(is_array($_data) || is_object($_data))
		{
			$_data = json_encode($_data, JSON_UNESCAPED_UNICODE);
		}

		self::$transaction_update[$_field] = $_data;
	}





}
?>