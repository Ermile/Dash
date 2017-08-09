<?php
namespace addons\content_account\changepass;
use \lib\utility;
use \lib\debug;

class model extends \mvc\model
{
	function post_changepass()
	{
		$myid    = $this->login('id');
		$newpass = utility::post('password-new');
		$oldpass = utility::post('password-old');
		$tmp_result = false;
		$force_change = false;
		$passlen 	= strlen(trim($newpass));
		if($passlen < 5)
		{
			debug::error(T_("Password length must be over five characters!"));
			return ;
		}elseif($passlen > 100)
		{
			debug::error(T_("Password length must be less 50 characters!"));
			return ;
		}
		if($myid)
		{
			$query =
			"
				SELECT *
				FROM users
				WHERE
					users.id = '$myid' AND
					users.user_status IN ('active', 'awaiting')
				LIMIT 1
			";
			$tmp_result = \lib\db::get($query, null, true);
		}
		elseif(isset($_SESSION['tmp']['verify_mobile']))
		{
			$force_change = true;
			$mobile = $_SESSION['tmp']['verify_mobile'];
			$query =
			"
				SELECT *
				FROM users
				WHERE
					users.user_mobile = '$mobile' AND
					users.user_status IN ('active', 'awaiting')
				LIMIT 1
			";
			$tmp_result = \lib\db::get($query, null, true);
			$myid = $tmp_result['id'];
		}
		// if exist
		if($tmp_result)
		{
			$myhashedPassword = $tmp_result['user_pass'];
			// if password is correct. go for login:)
			if (
				(isset($myhashedPassword) && utility::hasher($oldpass, $myhashedPassword))
				|| $force_change
				)
			{
				$newpass   = utility::post('password-new', 'hash');
				if(\lib\debug::$status)
				{
					$query ="UPDATE users SET user_pass = '$newpass' WHERE id = $myid";
					\lib\db::query($query);
					\lib\db\sessions::change_password($myid);
				}

				$this->commit(function()
				{
					debug::true(T_("change password successfully"));
					if(isset($_SESSION['tmp']['verify_mobile_referer']))
					{
						unset($_SESSION['tmp']['verify_mobile']);
						unset($_SESSION['tmp']['verify_mobile_time']);
						unset($_SESSION['tmp']['verify_mobile_referer']);
						$this->redirector()->set_url('login');
					}
					else
					{
						if(\lib\utility::get('referer'))
						{
							$this->referer();
						}
						else
						{
							debug::msg('direct', true);
							$this->redirector()->set_domain()->set_url();
						}
					}
				});

				// if a query has error or any error occour in any part of codes, run roolback
				$this->rollback(function() { debug::error(T_("change password failed!")); } );
			}

			// password is incorrect:(
			else
			{
				debug::error(T_("Password is incorrect"));
			}
		}
		// mobile does not exits
		elseif(!$tmp_result)
		{
			debug::error(T_("user is incorrect"));
		}

		// mobile exist more than 2 times!
		else
		{
			debug::error(T_("Please forward this message to administrator"));
		}
		// sleep(0.1);
	}
}
?>