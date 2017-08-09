<?php
namespace addons\content_account\verification;
use \lib\utility;
use \lib\debug;

class model extends \mvc\model
{
	public function post_verification()
	{
		$this->put_verification();
	}

	public function put_verification()
	{
		// get parameters and set to local variables
		$mycode     = utility::post('code');
		$mymobile   = utility::post('mobile','filter');
		if($mymobile == '' && isset($_SESSION['tmp']['verify_mobile']))
		{
			$mymobile = $_SESSION['tmp']['verify_mobile'];
		}

		$query = "SELECT id from users WHERE user_mobile = '$mymobile'";
		$myuserid = \lib\db::get($query, null, true);
		$myuserid = $myuserid['id'];
		// check for mobile exist
		$query = "SELECT id from logs WHERE
		user_id = '$myuserid' AND
		log_data = '$mycode' AND
		log_status = 'enable'";
		$tmp_result = \lib\db::get($query, null, true);
		if($tmp_result)
		{
			// mobile and code exist update the record and verify
			$query = "UPDATE logs SET log_status = 'expire' WHERE
			user_id = '$myuserid' AND
			log_data = '$mycode'";
			$tmp_result = \lib\db::query($query);

			$query = "UPDATE users SET user_status = 'active'
			WHERE id = '$myuserid'";
			$tmp_result = \lib\db::query($query);


			// ======================================================
			// you can manage next event with one of these variables,
			// commit for successfull and rollback for failed
			//
			// if query run without error means commit
			$this->commit(function($_mobile, $_userid)
			{
				if(method_exists('\lib\utility\users', 'verify'))
				{
					$args =
					[
						'user_id' => $_userid,
						'mobile'  => $_mobile,
						'port'    => 'site',
					];
					\lib\utility\users::verify($args);
				}

				if(isset($_SESSION['tmp']['verify_mobile_referer']))
				{
					$this->redirector($_SESSION['tmp']['verify_mobile_referer']);
				}
				else
				{
					$this->model()->setLogin($_userid, false);
					$this->referer();
					unset($_SESSION['tmp']['verify_mobile']);
					unset($_SESSION['tmp']['verify_mobile_time']);
				}
				$myreferer = utility\cookie::write('mobile', $_mobile, 60*5);
				$myreferer = utility\cookie::write('from', 'verification', 60*5);
				debug::true(T_("verify successfully.").' '.T_("please Input your new password"));
			}, $mymobile, $myuserid);

			// if a query has error or any error occour in any part of codes, run roolback
			$this->rollback(function() { debug::error(T_("verify failed!")); } );
		}

		// mobile does not exits
		elseif(!$tmp_result)
			debug::error(T_("this data is incorrect"));

		// mobile exist more than 2 times!
		else
			debug::error(T_("please forward this message to administrator"));
	}
}
?>