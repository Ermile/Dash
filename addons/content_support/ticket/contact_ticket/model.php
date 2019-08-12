<?php
namespace content_support\ticket\contact_ticket;

class model
{

	public static function check_input_time()
	{
		$session_name = 'contact_ticket_count';
		$time         = 60 * 3; // 3 min
		$max_count    = 3;      // 3 times

		$count = \dash\session::get($session_name);
		if($count)
		{
			\dash\session::set($session_name, $count + 1, null, $time);
		}
		else
		{
			\dash\session::set($session_name, 1, null, $time);
		}

		if($count >= $max_count && !\dash\permission::supervisor())
		{
			\dash\log::set('tryCount>inMins');
			\dash\notif::error(T_("You hit our maximum try limit."). ' '. T_("Try again later!"));
			return false;
		}

		return true;
	}

	/**
	 * save contact form
	 */
	public static function post()
	{
		if(!self::check_input_time())
		{
			return false;
		}

		$ticket_load_page_time = \dash\session::get('ticket_load_page_time');

		if($ticket_load_page_time)
		{
			if(time() - $ticket_load_page_time < 5)
			{
				\dash\session::set('ticket_load_page_time', time());
				\dash\header::status(422, T_("It was very fast!"));
			}
		}
		else
		{
			\dash\notif::warn(T_("Your cookies may have been blocked"). ' '. T_("You need to enable cookie for usign this service"));
			return false;
		}

		$post_content = null;
		$fn_args = func_get_args();
		if(isset($fn_args[0]) && is_string($fn_args[0]) && $fn_args[0])
		{
			$post_content = $fn_args[0];
		}

		// get the content
		if($post_content)
		{
			$content = $post_content;
		}
		else
		{
			$content = \dash\request::post("content");
		}

		// check content
		if($content == '' || !trim($content))
		{
			\dash\notif::error(T_("Please try type something!"), "content");
			return false;
		}

		if(strip_tags($_POST['content']) != $_POST['content'])
		{
			\dash\session::set('ticket_load_page_time', time());
			\dash\header::status(422, T_("You try to add some html code!"));
		}

		$count_http  = substr_count($content, 'http://');
		$count_https = substr_count($content, 'https://');
		if($count_https + $count_http >= 2)
		{
			\dash\header::status(422, T_("Can not set 2 link in one message!"));
		}

		// check login
		if(\dash\user::login())
		{
			// add new ticket
			$user_id = \dash\user::id();

			// get mobile from user login session
			$mobile = \dash\user::detail('mobile');

			if(!$mobile)
			{
				$mobile = \dash\request::post('mobile');
			}

			// get display name from user login session
			$displayname = \dash\user::detail("displayname");
			// user not set users display name, we get display name from contact form
			if(!$displayname)
			{
				$displayname = \dash\request::post("name");
			}
			// get email from user login session
			$email = \dash\user::detail('email');
			// user not set users email, we get email from contact form
			if(!$email)
			{
				$email = \dash\request::post("email");
			}

		}
		else
		{
			// users not registered
			$user_id     = null;
			$displayname = \dash\request::post("name");
			$email       = \dash\request::post("email");
			$mobile      = \dash\request::post("mobile");

			$content_temp = null;

			if($mobile)
			{
				$content_temp = T_("Mobile"). " ". $mobile. "\n";
			}

			if($email)
			{
				$content_temp .= T_("Email"). " ". $email. "\n";
			}

			if($displayname)
			{
				$content_temp .= T_("Name"). " ". $displayname. "\n";
			}

			$content = $content_temp. $content;
		}

		/**
		 * register user if set mobile and not register
		 */
		if($mobile && !\dash\user::login())
		{
			// check valid mobile
			$mobile = \dash\utility\convert::to_en_number($mobile);
			if($mobile = \dash\utility\filter::mobile($mobile))
			{
				// check existing mobile
				$exists_user = \dash\db\users::get_by_mobile($mobile);

				// register if the mobile is valid
				if(!$exists_user || empty($exists_user))
				{
					// signup user by site_guest
					$user_id = \dash\db\users::signup(['mobile' => $mobile, 'displayname' => $displayname]);

					if(!$user_id)
					{
						$user_id = null;
					}

					// save log by caller 'user:send:contact:register:by:mobile'
					\dash\log::set('contactRegisterByMobile');
				}
				elseif(isset($exists_user['id']))
				{
					$user_id = $exists_user['id'];
				}
			}
		}

		$args =
		[
			'author'  => $displayname,
			'email'   => $email,
			'type'    => 'ticket',
			'via'     => 'contact',
			'content' => $content,
			'title'   => \dash\temp::get('tempTicketTitle') ? \dash\temp::get('tempTicketTitle') : T_("Contact Us"),
			'mobile'  => $mobile,
			'user_id' => $user_id,

		];

		$result = \dash\app\ticket::add($args);

		if(isset($result['id']))
		{
			$log =
			[
				'from' => \dash\user::id() ? \dash\user::id() : $user_id,
				'code' => $result['id'],
				'via'  => 'contact',
			];

			\dash\log::set('ticket_addNewTicket', $log);
		}


		if(\dash\user::login())
		{
			if(isset($result['id']))
			{

				$ticket_link = '<a href="'. \dash\url::site(). '/support/ticket/show?id='. $result['id'].'">'. T_("You can check your contacting answer here") .'</a>';
				\dash\notif::ok(T_("Thank You For contacting us"). ' '. $ticket_link);
				\dash\redirect::pwd();
			}
			else
			{
				// just if we have error run this code
				\dash\log::set('contactUsLoginNotSave');
				\dash\notif::error(T_("We could'nt save the contact"));
			}
		}
		else
		{
			if(isset($result['codeurl']))
			{
				\dash\session::set('temp_ticket_codeurl', $result['codeurl']);
				$ticket_link = '<a href="'. $result['codeurl'].'">'. T_("You can check your contacting answer here") .'</a>';
				\dash\notif::ok(T_("Thank You For contacting us"). ' '. $ticket_link);
				\dash\redirect::pwd();

			}
			else
			{
				\dash\log::set('contactFail');
				\dash\notif::error(T_("We could'nt save the contact"));
			}
		}
	}
}
?>
