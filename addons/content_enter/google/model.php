<?php
namespace content_enter\google;


class model
{
	public static function get()
	{
		if(\dash\request::get('code'))
		{
			// check access from google
			$check = \dash\utility\google::check();
			if($check)
			{
				\dash\log::set('loginByGoogle');

				// go to what url
				$go_to_url           = null;

				$no_problem_to_login = false;

				$user_data = \dash\utility\google::user_info();
				// get user email
				$email = \dash\utility\google::user_info('email');
				// load data by email in field user google mail
				\dash\utility\enter::load_user_data($email, 'googlemail');
				// the user exist in system
				if(\dash\utility\enter::user_data('id'))
				{
					// check user status
					if(in_array(\dash\utility\enter::user_data('status'), ['filter', 'block']))
					{
						// the user was blocked
						\dash\utility\enter::next_step('block');
						// go to block page
						\dash\utility\enter::go_to('block');

						return false;
					}
					else
					{
						if(\dash\utility\enter::user_data('mobile'))
						{
							// no problem to login this user
							$no_problem_to_login = true;
						}
						else
						{
							if(\dash\utility\enter::user_data('dontwillsetmobile'))
							{
								if(strtotime(\dash\utility\enter::user_data('dontwillsetmobile')) > (60*60*24*365))
								{
									\dash\utility\enter::set_session('mobile_request_from', 'google_email_exist');

									\dash\utility\enter::set_session('logined_by_email', \dash\utility\google::user_info('email'));
									// go to mobile get to enter mobile
									\dash\utility\enter::next_step('mobile/request');
									// get go to url
									$go_to_url = 'mobile/request';
								}
								else
								{
									// no problem to login this user
									$no_problem_to_login = true;
								}
							}
							else
							{
								\dash\utility\enter::set_session('mobile_request_from', 'google_email_exist');

								\dash\utility\enter::set_session('logined_by_email', \dash\utility\google::user_info('email'));
								// go to mobile get to enter mobile
								\dash\utility\enter::next_step('mobile/request');
								// get go to url
								$go_to_url = 'mobile/request';
							}
						}
					}
				}
				else
				{
					// the email of this user is not exist in system
					$args = [];
					if(\dash\utility\google::user_info('name'))
					{
						$args['displayname'] = \dash\utility\google::user_info('name');
					}
					elseif(\dash\utility\google::user_info('familyName') || \dash\utility\google::user_info('givenName'))
					{
						$args['displayname'] = trim(\dash\utility\google::user_info('familyName'). ' '. \dash\utility\google::user_info('givenName'));
					}

					$args['email'] = \dash\utility\google::user_info('email');
					$args['datecreated']  = date("Y-m-d H:i:s");

					\dash\utility\enter::set_session('mobile_request_from', 'google_email_not_exist');

					\dash\log::set('loginByGoogleSignuped');

					\dash\utility\enter::set_session('must_signup', $args);

					\dash\utility\enter::set_session('logined_by_email', \dash\utility\google::user_info('email'));

					// go to mobile get to enter mobile
					\dash\utility\enter::next_step('mobile/request');
					// get go to url
					$go_to_url = 'mobile/request';
				}

				if($no_problem_to_login)
				{
					// set login session
					$redirect_url = \dash\utility\enter::enter_set_login();
					\dash\utility\enter::set_session('redirect_url', $redirect_url);
					// save redirect url in session to get from okay page
					// set okay as next step
					\dash\utility\enter::next_step('okay');
					// go to okay page
					\dash\utility\enter::go_to('okay');
				}
				else
				{
					// go to url
					if($go_to_url)
					{
						// in this time we need time to check next step
						// so we set for ever dont whill set mobile and go to next step
						// to login quick by google mail
						\dash\utility\enter::set_session('dont_will_set_mobile', true);
						// self::mobile_request_next_step();
						return;

						// \dash\utility\enter::go_to($go_to_url);
					}
					else
					{
						\dash\utility\enter::set_session('alert' ,['text' => T_("System error! try again")]);
						\dash\utility\enter::go_to('alert');
					}
				}
			}
			else
			{
				return false;
			}
		}
	}
}
?>