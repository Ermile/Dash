<?php
namespace addons\content_enter\verify\resend;


class controller extends \addons\content_enter\main\controller
{
	public function ready()
	{
		// bug fix two redirect to this page
		// if(isset($_SERVER['HTTP_ACCEPT']) && $_SERVER['HTTP_ACCEPT'] === '*/*')
		// {
		// 	return ;
		// }

		// if this step is locked go to error page and return
		if(\dash\utility\enter::lock('verify/resend'))
		{
			\dash\header::status(404, 'verify/resend');
			return;
		}

		// check method
		if(self::get_request_method() === 'get')
		{
			// if the user start my bot and wa have her chat id
			// if user start my bot try to send code to this use
			// if okay route this
			// else go to nex way

			if(\dash\utility\enter::get_session('send_code_at_time'))
			{
				if(time() - intval(\dash\utility\enter::get_session('send_code_at_time')) < self::$resend_after)
				{
					\dash\header::status(404, 'verify/resend/why/harry?');
					return;
				}
				else
				{
					// send code way
					\dash\utility\enter::go_to_verify();

					$args['force_json']   = false;
					$args['force_stop']   = true;
					$args['not_redirect'] = false;

					$this->_processor($args);
				}
			}
			else
			{
				\dash\header::status(404, 'verify/resend/time?');
			}
		}
		else
		{
			self::error_method('verify/resend');
		}

	}
}
?>