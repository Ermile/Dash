<?php
namespace content_enter\verify\later;


class view
{
	public static function config()
	{
		\content_enter\verify\view::verifyPageTitle();
		if(!\dash\utility\enter::get_session('sendsms_code_run'))
		{
			\content_enter\verify\sendsms\model::send_sendsms_code();
			\dash\utility\enter::set_session('sendsms_code_run', true);
		}

		if(\dash\utility\enter::get_session('sendsms_code'))
		{
			\dash\data::codeSend(\dash\utility\enter::get_session('sendsms_code'));
			\dash\data::codeSendNum('+98 1000 66600 66600');
			\dash\data::codeSendMsg(T_('Send ":code" to :num',
				[
					'code' => \dash\data::codeSend(),
					'num' => '<b><code>'. \dash\data::codeSendNum(). '</code></b>'
				]
				));
		}
	}
}
?>