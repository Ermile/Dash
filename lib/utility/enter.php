<?php
namespace dash\utility;

class enter
{

	public static function clean_session()
	{
		unset($_SESSION['enter']);
	}


	public static function set_session($_key, $_value)
	{
		if(!isset($_SESSION['enter']))
		{
			$_SESSION['enter'] = [];
		}

		$_SESSION['enter'][$_key] = $_value;
	}


	public static function get_session($_key)
	{
		if(isset($_SESSION['enter'][$_key]))
		{
			return $_SESSION['enter'][$_key];
		}
		return null;
	}


	/**
	 * Loads an user data.
	 */
	public static function load_user_data($_type = 'mobile', $_value)
	{
		$default_option =
		[
			'email_field' => 'googlemail',
		];

		if(!is_array($_option))
		{
			$_option = [];
		}
		$_option = array_merge($default_option, $_option);

		$data = [];

		switch ($_type)
		{
			// load contacts to find username or mobile or
			case 'usernameormobile':
				if($_value)
				{
					$data = \dash\db\users::find_user_to_login($_value);
				}
				break;

			// get user data by mobile
			case 'mobile':
				if($_value)
				{
					$data = \dash\db\users::get_by_mobile($_value);
				}
				break;

			// get use data by username
			case 'username':
				if($_value)
				{
					$data = \dash\db\users::get_by_username($_value);
				}
				break;

			// get user data by user id
			case 'user_id':
				if($_value)
				{
					$data = \dash\db\users::get_by_id($_value);
				}
				break;

			// get use data by email
			case 'email':
				if($_value)
				{
					$data = \dash\db\users::get_by_email($_value, $_option['email_field']);
				}
				break;

			default:
				# code...
				break;
		}

		if($data)
		{
			self::session_set('user_data', $data);
		}
		return $data;
	}


	/**
	 * { function_description }
	 *
	 * @param      <type>  $_key   The key
	 */
	public static function user_data($_key = null)
	{
		if(!isset($_SESSION['enter']['user_data']))
		{
			self::load_user_data('mobile');
		}

		if($_key)
		{
			if(isset($_SESSION['enter']['user_data'][$_key]))
			{
				return $_SESSION['enter']['user_data'][$_key];
			}
			return null;
		}

		if(isset($_SESSION['enter']['user_data']))
		{
			return $_SESSION['enter']['user_data'];
		}
		return null;
	}


	/**
	*	Signup new user
	*/
	public static function signup($_args = [])
	{

		$default_args =
		[
			'mobile'      => null,
			'displayname' => null,
			'password'    => null,
			'email'       => null,
			'status'      => 'awaiting'
		];

		if(is_array($_args))
		{
			$_args = array_merge($default_args, $_args);
		}

		\dash\utility\enter::session_set('first_signup', true);

		// save ref in users table
		if(isset($_SESSION['ref']) && !isset($_args['ref']))
		{
			$_args['ref'] = intval($_SESSION['ref']);
			unset($_SESSION['ref']);
		}

		$mobile = \dash\utility\enter::get_session('mobile');
		if($mobile)
		{
			// set mobile to use in other function
			$_value    = $mobile;
			$_args['mobile'] = $mobile;
			$_args['email']  = $_value;

			$user_id = \dash\db\users::signup_quick($_args);

			if($user_id)
			{
				self::load_user_data('mobile');
			}
			return $user_id;
		}
	}


	/**
	*	Signup new user
	*/
	public static function signup_email($_args = [])
	{
		if(\dash\utility\enter::get_session('dont_will_set_mobile'))
		{
			// $_args['dontwillsetmobile'] = date("Y-m-d H:i:s");
		}
		else
		{
			if(\dash\utility\enter::get_session('temp_mobile') && !isset($_args['mobile']))
			{
				$_args['mobile'] = \dash\utility\enter::get_session('temp_mobile');
			}
		}

		\dash\utility\enter::session_set('first_signup', true);

		// save ref in users table
		if(isset($_SESSION['ref']) && !isset($_args['ref']))
		{
			$_args['ref'] = intval($_SESSION['ref']);
			unset($_SESSION['ref']);
		}

		$user_id = \dash\db\users::insert($_args);

		if($user_id)
		{
			$_value = \dash\db::insert_id();
			self::load_user_data('user_id');
		}
		return $_value;

	}

	/**
	 * find redirect url
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function find_redirect_url($_url = null)
	{
		$host = \dash\url::base();
		if($_url)
		{
			return $_url;
		}
		// get url language
		// if have referer redirect to referer
		if(\dash\request::get('referer'))
		{
			$host = \dash\request::get('referer');
		}
		elseif(isset($_SESSION['enter_referer']) && $_SESSION['enter_referer'])
		{
			$host = $_SESSION['enter_referer'];
			unset($_SESSION['enter_referer']);
		}
		elseif(\dash\utility\enter::get_session('first_signup'))
		{
			// if first signup
			if(\dash\option::config('enter', 'singup_redirect'))
			{
				$host .= '/'. \dash\option::config('enter', 'singup_redirect');
			}
			else
			{
				$host .= \dash\option::config('redirect');
			}
		}
		else
		{

			$language = \dash\db\users::get_language(\dash\utility\enter::user_data('id'));
			// @check
			if($language && \dash\language::check($language))
			{

			}
			else
			{

			}

			$host .='/'. \dash\option::config('redirect');
		}

		return $host;
	}


	/**
	 * login if have remember me
	 */
	public static function login_by_remember($_url = null)
	{
		$cookie = \dash\db\sessions::get_cookie();
		if($cookie)
		{
			$user_id = \dash\db\sessions::get_user_id();
			if($user_id)
			{
				\dash\db\users::set_login_session($user_id);

				if(isset($_SESSION['main_account']))
				{
					// if the admin user login by this user
					// not save the session
				}
				else
				{
					\dash\db\sessions::set($user_id);
				}
			}
		}
	}


	/**
	 * login
	 */
	public static function enter_set_login($_url = null, $_auto_redirect = false)
	{

		\dash\db\users::set_login_session(\dash\utility\enter::user_data('id'));

		if(\dash\utility\enter::user_data('id'))
		{
			if(isset($_SESSION['main_account']) && isset($_SESSION['main_mobile']))
			{
				if(isset($_SESSION['user']['mobile']) && $_SESSION['user']['mobile'] === $_SESSION['main_mobile'])
				{
					\dash\db\sessions::set(\dash\utility\enter::user_data('id'));
				}
				// if the admin user login by this user
				// not save the session
			}
			else
			{
				// set remeber and save session
				\dash\db\sessions::set(\dash\utility\enter::user_data('id'));
				// check user status
				// if the user status is awaiting
				// set the user status as enable
				if(\dash\utility\enter::user_data('status') === 'awaiting' && is_numeric(\dash\utility\enter::user_data('id')))
				{
					\dash\db\users::update(['status' => 'active'], \dash\utility\enter::user_data('id'));
				}
			}
		}

		$url = self::find_redirect_url($_url);

		if($_auto_redirect)
		{
			// clean session
			self::clean_session();
			// go to new address
			self::go_redirect($url, false, true);
		}
		else
		{
			\dash\utility\enter::session_set('redirect_url', $url);
			return $url;
		}

	}


	/**
	 * Sets the logout.
	 *
	 * @param      <type>  $_user_id  The user identifier
	 */
	public static function set_logout($_user_id, $_auto_redirect = true)
	{

		if($_user_id && is_numeric($_user_id))
		{
			if(isset($_SESSION['main_account']) && isset($_SESSION['main_mobile']) && isset($_SESSION['user']['mobile']))
			{
				if($_SESSION['user']['mobile'] === $_SESSION['main_mobile'])
				{
					\dash\db\sessions::logout($_user_id);
				}
				// if the admin user login by this user
				// not save the session
			}
			else
			{
				// set this session as logout
				\dash\db\sessions::logout($_user_id);
			}
		}

		/**
		 * destroy user id
		 */
		\dash\user::destroy();

		$_SESSION['user']    = [];
		$_SESSION['contact'] = [];
		$_SESSION            = [];

		// unset and destroy session then regenerate it
		session_unset();
		if(session_status() === PHP_SESSION_ACTIVE)
		{
			session_destroy();
		}

		if($_auto_redirect)
		{
			// go to base
			self::go_to('main');
		}

	}

	/**
	 * return list of way we can send code to the user
	 *
	 * @param      <type>  $_mobile_or_email  The usernameormobile
	 *
	 * @return     array   ( description_of_the_return_value )
	 */
	public static function list_send_code_way()
	{
		$i_can     = false;
		$is_mobile = false;
		$is_email  = false;

		$mobile    = \dash\utility\enter::user_data('mobile');
		$email     = \dash\utility\enter::user_data('email');

		if(\dash\utility\filter::mobile($mobile))
		{
			$i_can     = true;
			$is_mobile = true;
		}

		if(preg_match("/^(.*)\@(.*)\.(.*)$/", $email))
		{
			$i_can    = true;
			$is_email = true;
		}


		$way = [];


		if($is_email)
		{
			// load email way
			// array_push($way, 'email');
		}

		if($is_mobile)
		{
			if(\dash\utility\enter::user_data('chatid') && \dash\option::social('telegram', 'status'))
			{
				if(\dash\option::config('enter', 'verify_telegram'))
				{
					array_push($way, 'telegram');
				}
			}

			if(\dash\utility\enter::user_data('mobile') && \dash\utility\filter::mobile(\dash\utility\enter::user_data('mobile')))
			{
				if(\dash\option::config('enter', 'verify_sms'))
				{
					array_push($way, 'sms');
				}

				if(\dash\option::config('enter', 'verify_call'))
				{
					array_push($way, 'call');
				}

				if(\dash\option::config('enter', 'verify_sendsms'))
				{
					array_push($way, 'sendsms');
				}

			}
		}

		if(\dash\url::isLocal() && empty($way))
		{
			array_push($way, 'sms');
		}


		if(!$i_can || empty($way))
		{
			self::open_lock('verify/what');
			self::next_step('verify/what');
			self::go_to('verify/what');
		}

		return $way;
	}


	/**
	 * Sends a code way.
	 */
	public static function send_code_way()
	{
		$host = \dash\url::base();
		$host .= '/enter/verify/';
		self::open_lock('verify');


		self::go_redirect($host);
	}


	/**
	 * Sends a way.
	 * find send way
	 * @param      string  $_type  The type [ send_rate | resend_rate]
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function send_way($_type = 'send_rate')
	{
		// generate verify code to find what old way
		// if no code was set
		// make new code and way is null
		// we find the way is the first way to send
		self::generate_verification_code();
		// get the old way code
		$old_way = \dash\utility\enter::get_session('verification_code_way');

		// get send rate by look at $_type
		if($_type == 'send_rate')
		{
			$rate = self::$send_rate;
		}
		elseif($_type == 'resend_rate')
		{
			$rate = self::$resend_rate;
		}
		else
		{
			$rate = self::$send_rate;
		}

		// first send way code
		if(!$old_way)
		{
			if(isset($rate[0]) && is_string($rate[0]))
			{
				if(\dash\utility\enter::get_session('verification_code_id'))
				{
					if(\dash\db\logs::update(['desc' => $rate[0]], \dash\utility\enter::get_session('verification_code_id')))
					{
						// update session on nex way
						\dash\utility\enter::session_set('verification_code_way', $rate[0]);
						// first way to send code
						return $rate[0];
					}
				}
			}
		}

		// find key of this old way
		$current_key = array_search($old_way, $rate);
		// if the key is find
		if(isset($current_key))
		{
			// go to nex key
			$next_key = $current_key + 1;
			if(isset($rate[$next_key]) && is_string($rate[$next_key]))
			{
				// nex way
				if(\dash\utility\enter::get_session('verification_code_id'))
				{
					// update log on next way
					if(\dash\db\logs::update(['desc' => $rate[$next_key]], \dash\utility\enter::get_session('verification_code_id')))
					{
						// update session on nex way
						\dash\utility\enter::session_set('verification_code_way', $rate[$next_key]);
						// return the way to got to this step
						return $rate[$next_key];
					}
				}
			}
		}
		return false;
	}


	/**
	 * Gets the last way.
	 * get last way of send rate
	 *
	 */
	public static function get_last_way($_type = 'send_rate')
	{
		// get the old way code
		$old_way = \dash\utility\enter::get_session('verification_code_way');

		// get send rate by look at $_type
		if($_type == 'send_rate')
		{
			$rate = self::$send_rate;
		}
		elseif($_type == 'resend_rate')
		{
			$rate = self::$resend_rate;
		}
		else
		{
			$rate = self::$send_rate;
		}

		// first send way code
		if(!$old_way)
		{
			$old_way = ':/';
		}

		// find key of this old way
		$current_key = array_search($old_way, $rate);
		// if the key is find
		if(isset($current_key))
		{
			// go to nex key
			$next_key = $current_key - 1;
			if(isset($rate[$next_key]) && is_string($rate[$next_key]))
			{
				return $rate[$next_key];
			}
		}

		if(isset($rate[0]) && is_string($rate[0]))
		{
			return $rate[0];
		}
		return false;
	}

	/**
	 * generate verification code
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function create_new_code($_way = null)
	{
		$code =  rand(10000,99999);
		if(self::$dev_mode)
		{
			$code = 11111;
		}
		// set verification code in session
		\dash\utility\enter::session_set('verification_code', $code);
		$time = date("Y-m-d H:i:s");

		$log_meta =
		[
			'data'     => $code,
			'desc' => $_way,
			'time'     => $time,
			'meta'     =>
			[
				'session' => $_SESSION,
			],
		];


		// save this code in logs table and session
		$log_id = \dash\db\logs::set('user:verification:code', \dash\utility\enter::user_data('id'), $log_meta);

		\dash\utility\enter::session_set('verification_code', $code);
		\dash\utility\enter::session_set('verification_code_time', $time);
		\dash\utility\enter::session_set('verification_code_way', $_way);
		\dash\utility\enter::session_set('verification_code_id', $log_id);

		return $code;
	}


	/**
	 * check code exist and live
	 */
	public static function generate_verification_code()
	{
		// check last code time and if is not okay make new code
		$last_code_ok = false;
		// get saved session last verification code

		if
		(
			\dash\utility\enter::get_session('verification_code') &&
			\dash\utility\enter::get_session('verification_code_id') &&
			\dash\utility\enter::get_session('verification_code_time')
		)
		{
			if(time() - strtotime(\dash\utility\enter::get_session('verification_code_time')) < self::$life_time_code)
			{
				// last code is true
				// need less to create new code
				$last_code_ok = true;
			}
		}


		// user code not found
		if(!$last_code_ok)
		{
			if(\dash\utility\enter::user_data('id'))
			{
				$where =
				[
					'caller'     => 'user:verification:code',
					'user_id'    => \dash\utility\enter::user_data('id'),
					'status' => 'enable',
					'limit'      => 1,
				];
				$log_code = \dash\db\logs::get($where);

				if($log_code)
				{
					if(isset($log_code['datecreated']) && time() - strtotime($log_code['datecreated']) < self::$life_time_code)
					{
						// the last code is okay
						// need less to create new code
						$last_code_ok = true;
						// save data in session
						if(isset($log_code['data']))
						{
							\dash\utility\enter::session_set('verification_code', $log_code['data']);
						}
						// save log time
						if(isset($log_code['datecreated']))
						{
							\dash\utility\enter::session_set('verification_code_time', $log_code['datecreated']);
						}
						// save log way
						if(isset($log_code['desc']))
						{
							\dash\utility\enter::session_set('verification_code_way', $log_code['desc']);
							if($prev_way = self::get_last_way())
							{
								\dash\utility\enter::session_set('verification_code_way', $prev_way);
							}
						}
						// save log id
						if(isset($log_code['id']))
						{
							\dash\utility\enter::session_set('verification_code_id', $log_code['id']);
						}

					}
					else
					{
						// the log is exist and the time of log is die
						// we expire the log
						if(isset($log_code['id']))
						{
							\dash\db\logs::update(['status' => 'expire'], $log_code['id']);
						}
					}
				}
			}
		}
		// if last code is not okay
		// make new code
		if(!$last_code_ok)
		{
			self::create_new_code();
		}
	}



	public static function check_code($_module)
	{
		$log_meta =
		[
			'meta' =>
			[
				'session' => $_SESSION,
				'post'    => \dash\request::post(),
			]
		];

		// if(!self::check_input_current_mobile())
		// {
		// 	\dash\notif::error(T_("Dont!"));
		// 	return false;
		// }

		if(!\dash\request::post('code'))
		{
			\dash\notif::error(T_("Please fill the verification code"), 'code');
			return false;
		}

		if(!is_numeric(\dash\request::post('code')))
		{
			\dash\notif::error(T_("What happend? the code is number. you try to send string!?"), 'code');
			return false;
		}

		$code_is_okay = false;
		// if the module is sendsms user not send the verification code here
		// the user send the verification code to my sms service
		// and this code is deffirent by verification code
		if($_module === 'sendsms')
		{
			$code = \dash\request::post('code');
			if($code == \dash\utility\enter::get_session('sendsms_code'))
			{
				$log_id = \dash\utility\enter::get_session('sendsms_code_log_id');

				if($log_id)
				{
					$get_log_detail = \dash\db\logs::get(['id' => $log_id, 'limit' => 1]);
					if(!$get_log_detail || !isset($get_log_detail['status']))
					{
						\dash\db\logs::set('enter:verify:sendsmsm:log:not:found', \dash\utility\enter::user_data('id'), $log_meta);
						\dash\notif::error(T_("System error, try again"));
						return false;
					}

					switch ($get_log_detail['status'])
					{
						case 'deliver':
							// the user must be login
							\dash\db\logs::update(['status' => 'expire'], $log_id);
							$code_is_okay = true;
							// set login session
							// $redirect_url = self::enter_set_login();

							// // save redirect url in session to get from okay page
							// \dash\utility\enter::session_set('redirect_url', $redirect_url);
							// // set okay as next step
							// self::next_step('okay');
							// // go to okay page
							// self::go_to('okay');
							break;

						case 'enable':
							// user not send sms or not deliver to us
							\dash\db\logs::set('enter:verify:sendsmsm:sms:not:deliver:to:us', \dash\utility\enter::user_data('id'), $log_meta);
							\dash\notif::error(T_("Your sms not deliver to us!"));
							return false;
							break;

						case 'expire':
							// the user user from this way and can not use this way again
							// this is a bug!
							\dash\db\logs::set('enter:verify:sendsmsm:sms:expire:log:bug', \dash\utility\enter::user_data('id'), $log_meta);
							\dash\notif::error(T_("What are you doing?"));
							return false;
						default:
							// bug!
							return false;
							break;
					}
				}
				else
				{
					\dash\db\logs::set('enter:verify:sendsmsm:log:id:not:found', \dash\utility\enter::user_data('id'), $log_meta);
					\dash\notif::error(T_("What are you doing?"));
					return false;
				}
			}
			else
			{
				\dash\db\logs::set('enter:verify:sendsmsm:user:inspected:change:html', \dash\utility\enter::user_data('id'), $log_meta);
				\dash\notif::error(T_("What are you doing?"));
				return false;
			}
		}
		else
		{
			if(intval(\dash\request::post('code')) === intval(\dash\utility\enter::get_session('verification_code')))
			{
				$code_is_okay = true;
			}
		}

		if($code_is_okay)
		{
			// expire code
			if(\dash\utility\enter::get_session('verification_code_id'))
			{
				// the user enter the code and the code is ok
				// must expire this code
				\dash\db\logs::update(['status' => 'expire'], \dash\utility\enter::get_session('verification_code_id'));
				\dash\utility\enter::session_set('verification_code', null);
				\dash\utility\enter::session_set('verification_code_time', null);
				\dash\utility\enter::session_set('verification_code_way', null);
				\dash\utility\enter::session_set('verification_code_id', null);
			}

			/**
			 ***********************************************************
			 * VERIFY FROM
			 * PASS/SIGNUP
			 * PASS/SET
			 * PASS/RECOVERY
			 ***********************************************************
			 */
			if(
				(
					\dash\utility\enter::get_session('verify_from') === 'signup' ||
					\dash\utility\enter::get_session('verify_from') === 'set' ||
					\dash\utility\enter::get_session('verify_from') === 'recovery'
				) &&
				\dash\utility\enter::get_session('temp_ramz_hash') &&
				is_numeric(\dash\utility\enter::user_data('id'))
			  )
			{
				// set temp ramz in use pass
				\dash\db\users::update(['password' => \dash\utility\enter::get_session('temp_ramz_hash')], \dash\utility\enter::user_data('id'));
			}


			/**
			 ***********************************************************
			 * VERIFY FROM
			 * USERNAME
			 * TRY TO REMOVE USER NAME
			 ***********************************************************
			 */
			if(\dash\utility\enter::get_session('verify_from') === 'username_remove' && is_numeric(\dash\utility\enter::user_data('id')))
			{
				// set temp ramz in use pass
				\dash\db\users::update(['username' => null], \dash\utility\enter::user_data('id'));
				// remove usename from sessions
				unset($_SESSION['user']['username']);
				// set the alert message
				self::set_alert(T_("Your username was removed"));
				// open lock of alert page
				self::next_step('alert');
				// go to alert page
				self::go_to('alert');
				return;
			}

			/**
			 ***********************************************************
			 * VERIFY FROM
			 * ENTER/DELETE
			 * DELETE ACCOUNT
			 ***********************************************************
			 */
			if(\dash\utility\enter::get_session('verify_from') === 'delete')
			{
				if(\dash\utility\enter::get_session('why'))
				{
					$update_meta  = [];

					$meta = \dash\utility\enter::user_data('meta');
					if(!$meta)
					{
						$update_meta['why'] = \dash\utility\enter::get_session('why');
					}
					elseif(is_string($meta) && substr($meta, 0, 1) !== '{')
					{
						$update_meta['other'] = $meta;
						$update_meta['why'] = \dash\utility\enter::get_session('why');
					}
					elseif(is_string($meta) && substr($meta, 0, 1) === '{')
					{
						$json = json_decode($meta, true);
						$update_meta = array_merge($json, ['why' => \dash\utility\enter::get_session('why')]);
					}

				}

				$update_user = [];
				if(!empty($update_meta))
				{
					$update_user['meta'] = json_encode($update_meta, JSON_UNESCAPED_UNICODE);
				}
				$update_user['status'] = 'removed';

				\dash\db\users::update($update_user, \dash\utility\enter::user_data('id'));

				\dash\db\sessions::delete_account(\dash\utility\enter::user_data('id'));

				//put logout
				self::set_logout(\dash\utility\enter::user_data('id'), false);
				self::next_step('byebye');
				self::go_to('byebye');
			}

			/**
			 ***********************************************************
			 * VERIFY FROM
			 * USERNAME/SET
			 * USERNAME/CHANGE
			 ***********************************************************
			 */
			if(
				(
					\dash\utility\enter::get_session('verify_from') === 'username_set' ||
					\dash\utility\enter::get_session('verify_from') === 'username_change'
				) &&
				\dash\utility\enter::get_session('temp_username') &&
				is_numeric(\dash\utility\enter::user_data('id'))
			  )
			{
				// set temp ramz in use pass
				\dash\db\users::update(['username' => \dash\utility\enter::get_session('temp_username')], \dash\utility\enter::user_data('id'));
				// set the alert message
				if(\dash\utility\enter::get_session('verify_from') === 'username_set')
				{
					self::set_alert(T_("Your username was set"));
				}
				else
				{
					self::set_alert(T_("Your username was change"));
				}

				if(isset($_SESSION['user']) && is_array($_SESSION['user']))
				{
					$_SESSION['user']['username'] = \dash\utility\enter::get_session('temp_username');
				}

				// open lock of alert page
				self::next_step('alert');
				// go to alert page
				self::go_to('alert');
				return;
			}

			/**
			 ***********************************************************
			 * VERIFY FROM
			 * MOBILI/REQUEST
			 ***********************************************************
			 */
			//////////////////////////////////////////////////////////////////////////////////////////////////////////////	MUST CHECK //////////////////////////////////
			if(\dash\utility\enter::get_session('verify_from') === 'mobile_request')
			{
				// must loaded mobile data
				if(\dash\utility\enter::get_session('temp_mobile') && is_numeric(\dash\utility\enter::get_session('temp_mobile')))
				{
					$load_mobile_data = \dash\db\users::get_by_mobile(\dash\utility\enter::get_session('temp_mobile'));
					if($load_mobile_data && isset($load_mobile_data['id']))
					{
						if(isset($load_mobile_data['status']) && in_array($load_mobile_data['status'], self::$block_status))
						{
							self::next_step('block');
							self::go_to('block');
							return ;
						}
						else
						{
							if(\dash\utility\enter::get_session('mobile_request_from') === 'google_email_not_exist')
							{
								if(isset($load_mobile_data['googlemail']) && $load_mobile_data['googlemail'])
								{
									if(\dash\utility\enter::get_session('logined_by_email') === $load_mobile_data['googlemail'])
									{
										self::$user_id = $load_mobile_data['id'];
										self::load_user_data('user_id');
									}
									else
									{
										\dash\utility\enter::session_set('old_google_mail', $load_mobile_data['googlemail']);
										\dash\utility\enter::session_set('new_google_mail', \dash\utility\enter::get_session('logined_by_email'));
										\dash\utility\enter::session_set('user_id_must_change_google_mail', $load_mobile_data['id']);
										// request from user to change email
										self::next_step('email/change/google');
										self::go_to('email/change/google');
										return ;
									}
								}
								else
								{
									\dash\db\users::update(['googlemail' => \dash\utility\enter::get_session('logined_by_email')], $load_mobile_data['id']);
									self::$user_id = $load_mobile_data['id'];
									self::load_user_data('user_id');
								}
							}
							else //if(\dash\utility\enter::get_session('mobile_request_from') === 'google_email_exist') or more
							{
								\dash\utility\enter::session_set('request_delete_msg', T_("Duplicate account"));

								self::next_step('delete/request');
								self::go_to('delete/request');
								return ;
							}
						}
					}
					else
					{
						if(\dash\utility\enter::get_session('mobile_request_from') === 'google_email_not_exist')
						{
							if(\dash\utility\enter::get_session('must_signup') && is_array(\dash\utility\enter::get_session('must_signup')))
							{
								$signup = \dash\utility\enter::get_session('must_signup');
								if(\dash\utility\enter::get_session('temp_mobile'))
								{
									$signup['mobile'] = \dash\utility\enter::get_session('temp_mobile');
								}

								if(\dash\utility\enter::get_session('logined_by_email'))
								{
									$signup['googlemail'] = \dash\utility\enter::get_session('logined_by_email');
								}

								$signup['status'] = 'active';
								\dash\utility\enter::session_set('first_signup', true);
								self::$user_id = \dash\db\users::signup($signup);
								self::load_user_data('user_id');
							}
							else
							{
								\dash\db\logs::set('error110000');
							}
						}
						elseif(\dash\utility\enter::get_session('mobile_request_from') === 'google_email_exist')
						{
							if(!\dash\utility\enter::user_data('mobile'))
							{
								\dash\db\users::update(['mobile' => \dash\utility\enter::get_session('temp_mobile')], \dash\utility\enter::user_data('id'));
								// login
							}
							self::$user_id = \dash\utility\enter::user_data('id');
							self::load_user_data('user_id');

						}
						else
						{
							// other way go to here
							// facebook not exist email and ...
							\dash\db\logs::set('error110');
							return false;
						}
					}
				}
				else
				{
					// no mobile was found :|
					// bug. return false;
					\dash\db\logs::set('error11');
					return false;
				}
			}
			//////////////////////////////////////////////////////////////////////////////////////////////////////////////	MUST CHECK //////////////////////////////////


			/**
			 ***********************************************************
			 * VERIFY FROM
			 * EMAIL/SET
			 * EMAIL/CHANGE
			 ***********************************************************
			 */
			if(
				(
					\dash\utility\enter::get_session('verify_from') === 'email_set' ||
					\dash\utility\enter::get_session('verify_from') === 'email_change'
				) &&
				\dash\utility\enter::get_session('temp_email') &&
				is_numeric(\dash\utility\enter::user_data('id'))
			  )
			{
				// set temp ramz in use pass
				\dash\db\users::update(['email' => \dash\utility\enter::get_session('temp_email')], \dash\utility\enter::user_data('id'));
			}

			/**
			 ***********************************************************
			 * VERIFY FROM
			 * TWO STEP VERICICATION
			 ***********************************************************
			 */
			if(\dash\utility\enter::get_session('verify_from') === 'two_step' &&	is_numeric(\dash\utility\enter::user_data('id')))
			{
				// no thing yet
			}


			/**
			 ***********************************************************
			 * VERIFY FROM
			 * TWO STEP VERICICATION SET
			 ***********************************************************
			 */
			if(\dash\utility\enter::get_session('verify_from') === 'two_step_set' &&	is_numeric(\dash\utility\enter::user_data('id')))
			{
				// set on two_step of this user
				\dash\db\users::update(['twostep' => 1], \dash\utility\enter::user_data('id'));
			}


			/**
			 ***********************************************************
			 * VERIFY FROM
			 * TWO STEP VERICICATION SET
			 ***********************************************************
			 */
			if(\dash\utility\enter::get_session('verify_from') === 'two_step_unset' &&	is_numeric(\dash\utility\enter::user_data('id')))
			{
				// set off two_step of this user
				\dash\db\users::update(['twostep' => 0], \dash\utility\enter::user_data('id'));
			}

			// set login session
			$redirect_url = self::enter_set_login();

			// save redirect url in session to get from okay page
			\dash\utility\enter::session_set('redirect_url', $redirect_url);
			// set okay as next step
			self::next_step('okay');
			// go to okay page
			self::go_to('okay');

		}
		else
		{
			// wrong code sleep code
			self::sleep_code();

			// plus count invalid code
			self::plus_try_session('invalid_code');

			\dash\notif::error(T_("Invalid code, try again"), 'code');
			return false;
		}
	}


	/**
	 * Sends a code email.
	 * send verification code whit email address
	 */
	public static function send_code_email()
	{
		$email = \dash\utility\enter::get_session('temp_email');
		$code  = self::generate_verification_code();
		$mail =
		[
			'to'      => $email,
			'subject' => 'contact',
			'body'    => "salam". $code,
		];
		// $mail = \dash\mail::send($mail);
		return $mail;
	}



	/**
	 * user fill the mobile/request
	 * this function find next step
	 * signup user
	 * or login only
	 */
	public static function mobile_request_next_step()
	{
		// set temp ramz in use pass
		switch (\dash\utility\enter::get_session('mobile_request_from'))
		{
			case 'google_email_not_exist':
				if(\dash\utility\enter::get_session('must_signup'))
				{
					// sign up user
					\dash\utility\enter::session_set('first_signup', true);

					$user_id = self::signup_email(\dash\utility\enter::get_session('must_signup'));
					if($user_id)
					{
						self::$user_id = $user_id;
						self::load_user_data('user_id');
						// auto redirect to redirect url
						self::enter_set_login(null, true);
						return;
					}
					else
					{
						// can not signup
						return false;
					}
				}
				break;

			case 'google_email_exist':
				if(is_numeric(\dash\utility\enter::user_data('id')))
				{
					// the user click on dont will mobile
					// we save this time to dontwillsetmobile to never show this message again
					$update_user_google = [];

					if(\dash\utility\enter::get_session('dont_will_set_mobile'))
					{
						$update_user_google['dontwillsetmobile'] = date("Y-m-d H:i:s");
					}
					if(!empty($update_user_google))
					{
						\dash\db\users::update($update_user_google, \dash\utility\enter::user_data('id'));
					}
					//auto redirect to redirect url
					self::enter_set_login(null, true);
					return ;
				}

				return false;
				break;

			default:
				# code...
				break;
		}
		return true;
	}
}
?>