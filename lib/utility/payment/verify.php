<?php
namespace dash\utility\payment;


class verify
{
	public static $user_id = null;

	public static $log_data = null;
	/**
	 * set config
	 * set user id to save log for this user id
	 */
	public static function config()
	{
		if(!self::$user_id && isset($_SESSION['user']['id']))
		{
			self::$user_id = $_SESSION['user']['id'];
		}
	}


	/**
	 * after complete pay operation
	 * reidrect to turn_back url
	 *
	 * @param      <type>  $_transaction_id  The transaction identifier
	 */
	public static function turn_back($_transaction_id = null)
	{
		$turn_back = null;
		if($_transaction_id && isset($_SESSION['turn_back'][$_transaction_id]))
		{
			$turn_back = $_SESSION['turn_back'][$_transaction_id];
		}
		else
		{
			$host      = \dash\url::site();
			$turn_back =  $host;
			$turn_back .= $lang;

			if(\dash\option::config('redirect'))
			{
		        $turn_back .= '/'. \dash\option::config('redirect');
			}
		}

		if(!$turn_back)
		{
			$turn_back = \dash\url::site();
		}

		// redirect to turn back url
        \dash\redirect::to($turn_back);
	}


	public static function clear_session()
	{
        \dash\session::set('payment_request_start', false);
        \dash\session::set('payment_verify_amount', null);
        \dash\session::set('payment_verify_status', null);
	}


	public static function get_amount()
	{
  		$amount = \dash\session::get('payment_verify_amount');

		if($amount)
		{
			return $amount;
		}

		return null;
	}


	public static function get_status()
	{
        $status = \dash\session::get('payment_verify_status');

        if($status)
        {
	        if($status === 'ok')
	        {
	        	return true;
	        }
	        elseif(in_array($status, ['error', 'verify_error']))
	        {
	        	return false;
	        }
        }

 		$start = \dash\session::get('payment_request_start');

 		if($start)
 		{
 			return false;
 		}
	}


	use verify\zarinpal;
	use verify\parsian;
	use verify\irkish;
	use verify\payir;
	use verify\asanpardakht;


}
?>