<?php
namespace addons\content_account\recovery;
use \lib\utility;
use \lib\debug;

class model extends \mvc\model
{
	public function post_recovery()
	{
		// get parameters and set to local variables
		$mymobile   = utility::post('mobile','filter');

		if(!$mymobile)
		{
			\lib\debug::error(T_("Please enter a valid mobile phone number"));
			return ;
		}
		// check for mobile exist
		$query =
		"
			SELECT *
			FROM  users
			WHERE
				users.user_mobile = '$mymobile' AND
				users.user_status IN ('active', 'removed', 'awaiting')
			LIMIT 1
		";
		$tmp_result = \lib\db::get($query, null, true);
		if($tmp_result)
		{
			$myuserid  = $tmp_result['id'];
			$query ="SELECT id FROM logitems WHERE logitems.logitem_title = 'account/recovery' LIMIT 0,1";
			$mylogitem = \lib\db::get($query, null, true);
			if(!$mylogitem)
				return;
			$mylogitem = $mylogitem['id'];

			$query ="SELECT log_data FROM logs WHERE
			logitem_id 		= '$mylogitem' AND
			user_id 		= '$myuserid' AND
			log_status 		= 'enable' LIMIT 0,1";
			$is_loged = \lib\db::get($query, null, true);

			if($is_loged)
			{
				$mycode = $is_loged['log_data'];
			}
			else
			{
				$mycode    = utility::randomCode();
				$date = date('Y-m-d H:i:s');
				$query ="INSERT INTO logs SET
				logitem_id 		= '$mylogitem',
				user_id 		= '$myuserid',
				log_data 		= '$mycode',
				log_status 		= 'enable',
				log_createdate 	= '$date'";
				\lib\db::query($query);
			}

			// ======================================================
			// you can manage next event with one of these variables,
			// commit for successfull and rollback for failed
			//
			// if query run without error means commit
			$this->commit(function($_mobile, $_code)
			{
				$myreferer = utility\cookie::read('referer');
				//Send SMS

				$service_name = \lib\router::get_domain(count(\lib\router::get_domain(-1))-2);
				$request = [
					'mobile' 		=> $_mobile,
					'template' 		=> $service_name . '-' . \lib\define::get_language(),
					'token'			=> $_code,
					'type'			=> 'call'
					];
				\lib\utility\sms::send($request, 'verify');
				// \lib\utility\sms::send(['mobile' => $_mobile, 'msg'=> 'recovery', 'arg' =>$_code]);
				debug::true(T_("we send a verification code for you"));
				$myreferer = utility\cookie::write('mobile', $_mobile, 60*5);
				$myreferer = utility\cookie::write('from', 'recovery', 60*5);
				$this->redirector()->set_url('verification');
				$_SESSION['tmp']['verify_mobile'] = $_mobile;
				$_SESSION['tmp']['verify_mobile_time'] = time() + (5*60);
				$_SESSION['tmp']['verify_mobile_referer'] = 'changepass';

			}, $mymobile, $mycode);

			// if a query has error or any error occour in any part of codes, run roolback
			$this->rollback(function() { debug::error(T_("recovery failed!")); } );
		}

		// mobile does not exits
		else
		{
			debug::error(T_("Mobile number is incorrect"));
		}
	}
}
?>