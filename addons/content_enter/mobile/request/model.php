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
		// if(\lib\utility::post('will') === 'no')
		// {
			// the user dont whill to enter mobile :/
			// never ask this question at this user
			self::set_enter_session('dont_will_set_mobile', true);

			self::mobile_request_next_step();

			return;
		// }

		// if(!\lib\utility::post('mobile'))
		// {
		// 	\lib\debug::error(T_("Please enter mobile or skip this step"));
		// 	return false;
		// }

		// $mobile = \lib\utility\filter::mobile(\lib\utility::post('mobile'));

		// if(!$mobile)
		// {
		// 	\lib\debug::error(T_("Please enter a valid mobile number"));
		// 	return false;
		// }

		// self::set_enter_session('verify_from', 'mobile_request');
		// self::set_enter_session('temp_mobile', $mobile);
		// self::send_code_way();
		// return;
	}
}
?>