<?php
namespace addons\content_enter\verify\sendsms;


class model extends \addons\content_enter\main\model
{

	/**
	 * send verification code to the user sendsms
	 *
	 * @param      <type>  $_chat_id  The chat identifier
	 * @param      <type>  $_text     The text
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public function send_sendsms_code()
	{

		if(\dash\utility\enter::user_data('id'))
		{
			$user_id = \dash\utility\enter::user_data('id');
		}
		else
		{
			return false;
		}

		$code = rand(10000,99999);

		\dash\utility\enter::session_set('sendsms_code', $code);

		$log_id = \dash\db\logs::set('enter:get:sms:from:user', $user_id, ['data' => $code, 'meta' => ['session' => $_SESSION]]);

		\dash\utility\enter::session_set('sendsms_code_log_id', $log_id);

		return true;
	}


	/**
	* check sended code
	*/
	public function post_verify()
	{
		self::check_code('sendsms');
	}
}
?>
