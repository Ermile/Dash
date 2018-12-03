<?php
namespace content_enter\verify\sendsms;


class model
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

		\dash\utility\enter::set_session('sendsms_code', $code);

		$save_log =
		[
			'to'   => $user_id,
			'code' => $code,
		];

		$log_id = \dash\log::set('enterGetSmsFromUser', $save_log);

		\dash\utility\enter::set_session('sendsms_code_log_id', $log_id);

		return true;
	}


	/**
	* check sended code
	*/
	public function post()
	{
		\dash\utility\enter::check_code('sendsms');
	}
}
?>
