<?php
namespace addons\content_enter\signup;
use \lib\utility;
use \lib\debug;
use \lib\db;

class model extends \addons\content_enter\main\model
{

	/**
	 * Posts an enter.
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function post_signup($_args)
	{
		if(utility::post('password'))
		{
			debug::error(T_("Dont!"));
			return false;
		}

		$mobile = utility::post('mobile');
		if(!$mobile)
		{
			debug::error(T_("Pleaes set mobile number"));
			return false;
		}

		$mobile = \lib\utility\filter::mobile($mobile);
		if(!$mobile)
		{
			debug::error(T_("Pleaes set a valid mobile number"));
			return false;
		}

		$username = utility::post('username');
		if(!$username || mb_strlen($username) < 5 || mb_strlen($username) > 50)
		{
			debug::error(T_("Pleaes set a valid username"));
			return false;
		}

		if(!preg_match("/[A-Za-z0-9\_]/", $username))
		{
			debug::error(T_("Username must in [A-Za-z0-9]"));
			return false;
		}

		$ramz = utility::post('ramzNew');
		if(!$ramz || mb_strlen($ramz) < 5 || mb_strlen($ramz) > 50)
		{
			debug::error(T_("Pleaes set a valid password"));
			return false;
		}

		$displayname = utility::post('displayname');
		if(!$displayname || mb_strlen($displayname) > 50)
		{
			debug::error(T_("Invalid full name"));
			return false;
		}

		$check_username = \lib\db\users::get_by_username($username);
		if($check_username)
		{
			debug::error(T_("This username is already taken."));
			return false;
		}

		$check_mobile = \lib\db\users::get_by_mobile($mobile);
		if($check_mobile)
		{
			debug::error(T_("This mobile is already signuped. You can login by this mobile"));
			return false;
		}

		$signup =
		[
			'mobile'      => $mobile,
			'displayname' => $displayname,
			'password'    => \lib\utility::hasher($ramz),
			'username'    => $username,
			'status'      => 'awaiting'
		];

		if(!debug::$status)
		{
			return false;
		}

		$user_id = \lib\db\users::signup_quick($signup);

		if(!$user_id)
		{
			debug::error(T_("We can not signup you"));
			return false;
		}

		self::$user_id = $user_id;
		self::load_user_data('user_id');
		self::enter_set_login(null, true);

	}
}
?>