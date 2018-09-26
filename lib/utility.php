<?php
namespace dash;

class utility
{

	/**
	 * Call this funtion for encode or decode your password.
	 * If you pass hashed password func verify that,
	 * else create a new pass to save in db
	 * @param  [type] $_plainPassword  [description]
	 * @param  [type] $_hashedPassword [description]
	 * @return [type]                  [description]
	 */
	public static function hasher($_plainPassword, $_hashedPassword = null, $_not_check_crazy = false)
	{
		$raw_password   = $_plainPassword;
		// custom text to add in start and end of password
		$mystart        = '^_^$~*~';
		$myend          = '~_~!^_^';
		$_plainPassword = $mystart. $_plainPassword. $myend;
		$_plainPassword = md5($_plainPassword);
		$myresult       = null;
		// if requrest verify pass check with
		if($_hashedPassword)
		{
			$myresult = password_verify($_plainPassword, $_hashedPassword);
		}
		else
		{
			if(!$_not_check_crazy)
			{
				if(\dash\utility\passwords::is_crazy($raw_password))
				{
					\dash\log::set('useCreazyPassword');
					\dash\notif::error(T_("This password is very simple and guessable, please use stronger password!"));
					return false;
				}
			}

			// create option for creating hash cost
			$myoptions = ['cost' => 7 ];
			$myresult  = password_hash($_plainPassword, PASSWORD_BCRYPT, $myoptions);
		}

		return $myresult;
	}
}
?>