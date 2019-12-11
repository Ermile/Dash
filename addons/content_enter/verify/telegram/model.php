<?php
namespace content_enter\verify\telegram;


class model
{

	public static function post()
	{
		\dash\utility\enter::check_code('telegram');
	}


	/**
	 * send verification code to the user telegram
	 *
	 * @param      <type>  $_chat_id  The chat identifier
	 * @param      <type>  $_text     The text
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function send_telegram_code()
	{
		// the telegram is off for this project
		if(!\dash\option::social('telegram', 'status'))
		{
			return false;
		}

		$my_chat_id = null;


		if(\dash\utility\enter::user_data('id'))
		{
			$my_chat_id = \dash\utility\enter::load_chat_id(\dash\utility\enter::user_data('id'));
		}
		elseif(\dash\utility\enter::get_session('temp_chat_id'))
		{
			$my_chat_id = \dash\utility\enter::get_session('temp_chat_id');
		}
		elseif(\dash\user::id())
		{
			$my_chat_id = \dash\utility\enter::load_chat_id(\dash\user::id());
		}

		if(!$my_chat_id)
		{
			return false;
		}


		\dash\utility\enter::generate_verification_code();

		$code = \dash\utility\enter::get_session('verification_code');
		// make text
		$text = '';
		// $text .= T_("Your login code is :code", ['code' => \dash\utility\human::number($code)]);
		$text .= T_("Your login code is :code", ['code' => '<code>'. $code. '</code>']);
		$text .= "\n\n". T_("This code can be used to log in to your account. Do not give it to anyone!"). ' ' . T_("If you didn't request this code, ignore this message.");

		if(\dash\url::isLocal())
		{
			return true;
		}
		\dash\log::set('telegram');

		$myData   = ['chat_id' => $my_chat_id, 'text' => $text];
		$myResult = \dash\social\telegram\tg::sendMessage($myData);

		// \dash\utility\telegram::sendMessage($my_chat_id, $text);
		return true;
	}
}
?>
