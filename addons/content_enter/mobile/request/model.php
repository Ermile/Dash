<?php
namespace addons\content_enter\mobile\request;


class model extends \addons\content_enter\main\model
{
	// load mobile data
	public $mobile_data = [];

	public $temp_mobile = [];


	/**
	 * Gets the enter.
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function post_mobile($_args)
	{
		// IN THIS TIME EVERYONE CLICK ON NO BUTTON
		// THE MOBILE AND EMAIN MUST BE CHECK !!!
		// if(\dash\request::post('will') === 'no')
		// {
			// the user dont whill to enter mobile :/
			// never ask this question at this user
			\dash\utility\enter::session_set('dont_will_set_mobile', true);

			self::mobile_request_next_step();

			return;
		// }

		// if(!\dash\request::post('mobile'))
		// {
		// 	\dash\notif::error(T_("Please enter mobile or skip this step"));
		// 	return false;
		// }

		// $mobile = \dash\utility\filter::mobile(\dash\request::post('mobile'));

		// if(!$mobile)
		// {
		// 	\dash\notif::error(T_("Please enter a valid mobile number"));
		// 	return false;
		// }

		// \dash\utility\enter::session_set('verify_from', 'mobile_request');
		// \dash\utility\enter::session_set('temp_mobile', $mobile);
		// \dash\utility\enter::send_code_way();
		// return;
	}
}
?>