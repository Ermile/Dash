<?php
namespace content_support\ticket\add;

class model
{

	public static function upload_file($_name)
	{
		if(\dash\request::files($_name))
		{
			$uploaded_file = \dash\app\file::upload(['debug' => false, 'upload_name' => $_name, 'max_upload' => 5*1024*1024]);

			if(isset($uploaded_file['url']))
			{
				return $uploaded_file['url'];
			}
			// if in upload have error return
			if(!\dash\engine\process::status())
			{
				return false;
			}
		}
		return null;
	}


	public static function add_new($_via, $_content, $_file = null, $_title = null)
	{
		// ready to insert tickets
		$args =
		[
			'title'   => $_title,
			'author'  => \dash\user::detail('displayname'),
			'email'   => \dash\user::detail('email'),
			'type'    => 'ticket',
			'content' => $_content,
			'via'     => $_via,
			'mobile'  => \dash\user::detail("mobile"),
			'file'    => $_file,
			'user_id' => \dash\user::id(),
		];

		// insert tickets
		$result = \dash\app\ticket::add($args);

		if(isset($result['id']))
		{
			$log =
			[
				'from'     => \dash\user::id(),
				'code'     => $result['id'],
				'masterid' => $result['id'],
				'via'      => $_via,
			];

			\dash\log::set('ticket_addNewTicket', $log);

			\dash\notif::ok(T_("Your ticket was sended"));
		}
		return $result;
	}

	public static function post()
	{

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


		$file     = self::upload_file('file');

		// we have an error in upload file1
		if($file === false)
		{
			return false;
		}

		if(\dash\permission::check('supportTicketSignature'))
		{
			$content = \dash\request::post('content') ? $_POST['content'] : null;
		}
		else
		{
			if(strip_tags($_POST['content']) != $_POST['content'])
			{
				\dash\session::set('ticket_load_page_time', time());
				\dash\header::status(422, T_("You try to send some html code!"));
			}

			$content = \dash\request::post('content');
		}

		$count_http  = substr_count($content, 'http://');
		$count_https = substr_count($content, 'https://');
		if($count_https + $count_http >= 2)
		{
			\dash\header::status(422, T_("Can not set 2 link in one message!"));
		}

		$title = null;
		if(\dash\request::get('title'))
		{
			$title = \dash\request::get('title');
			$title = substr($title, 0, 20);
		}

		// insert tickets
		$result = self::add_new('site', $content, $file, $title);

		if(isset($result['id']))
		{
			if(!\dash\user::login())
			{
				if(!isset($_SESSION['guest_ticket']) || (isset($_SESSION['guest_ticket']) && !is_array($_SESSION['guest_ticket'])))
				{
					$_SESSION['guest_ticket'] = [];
				}

				array_push($_SESSION['guest_ticket'], $result);

				if(isset($result['code']))
				{
					\dash\redirect::to(\dash\url::this().'/show?id='. $result['id']. '&guest='. $result['code']);
				}
			}
			else
			{
				\dash\redirect::to(\dash\url::this().'/show?id='. $result['id']);
			}
		}
	}
}
?>