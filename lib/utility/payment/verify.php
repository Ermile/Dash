<?php
namespace lib\utility\payment;
use \lib\debug;
use \lib\option;
use \lib\utility;
use \lib\db\logs;

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
			$host      = Protocol."://" . \lib\router::get_root_domain();
			$lang      = \lib\define::get_current_language_string();
			$turn_back =  $host;
			$turn_back .= $lang;

			if(\lib\option::config('redirect'))
			{
		        $turn_back .= '/'. \lib\option::config('redirect');
			}
		}

		if(!$turn_back)
		{
			$turn_back = Protocol."://" . \lib\router::get_root_domain();
		}

		// redirect to turn back url
        (new \lib\redirector($turn_back))->redirect();
	}

	public static function clear_session()
	{
        \lib\session::set('payment_request_start', null);
        \lib\session::set('payment_verify_amount', null);
        \lib\session::set('payment_verify_status', null);
        \lib\session::set('payment_request_start', null);

	}

	public static function get_amount()
	{
  		$amount = \lib\session::get('payment_verify_amount');
  		if($_get_amount)
  		{
  			if($amount)
  			{
  				return $amount;
  			}
  			else
  			{
  				return null;
  			}
  		}
	}


	public static function get_status($_get_amount = false)
	{
        $status = \lib\session::get('payment_verify_status');

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

 		$start = \lib\session::get('payment_request_start');

 		if($start)
 		{
 			return false;
 		}
	}


	use verify\zarinpal;
	use verify\parsian;
	use verify\irkish;


}
?>