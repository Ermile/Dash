<?php
namespace content_support\ticket\show;

class model
{

	/**
	 * UploAads an thumb.
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public static function upload_file($_name)
	{
		if(\dash\request::files($_name))
		{
			$uploaded_file = \dash\app\file::upload(['debug' => false, 'upload_name' => $_name]);

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


	public static function post()
	{
		$file     = self::upload_file('file');

		// we have an error in upload file1
		if($file === false)
		{
			return false;
		}

		if(\dash\request::post('TicketFormType') === 'tag')
		{
			\dash\permission::access('supportTicketAssignTag');

			if(!\dash\permission::check('cpTagSupportAdd'))
			{
				$current_tag = \dash\db\terms::get(['type' => 'support_tag']);
				if(is_array($current_tag))
				{
					$tag_titles = array_column($current_tag, 'title');
					$new_tag    = \dash\request::post('tag');
					$new_tag    = explode(',', $new_tag);
					foreach ($new_tag as $key => $value)
					{
						if(!in_array($value, $tag_titles))
						{
							\dash\notif::error(T_("Please select tag from list"), 'tag');
							return false;
						}
					}
				}
			}

			\dash\app::variable(['support_tag' => \dash\request::post('tag')]);
			\dash\app\posts::set_post_term(\dash\request::get('id'), 'support_tag', 'comments');
			\dash\log::set('ticketAddTag', ['code' => \dash\request::get('id'), 'tag' => \dash\request::post('tag')]);
			\dash\notif::ok(T_("Tag was saved"));

			if(!\dash\request::post('content'))
			{
				\dash\redirect::pwd();
				return true;
			}
		}


		$main = \dash\app\ticket::get(\dash\request::get('id'));

		if(!$main || !array_key_exists('user_id', $main))
		{
			\dash\header::status(403, T_("Ticket not found"));
		}

		// check is my ticket and some permission to load guest , ...
		$is_my_ticket = \content_support\ticket\show\view::is_my_ticket($main);


		if(\dash\request::post('TicketFormType') === 'changeStatus')
		{
			$status = \dash\request::post('status');

			if(!in_array($status, ['close','deleted','awaiting']))
			{
				\dash\notif::error(T_("Invalid status"));
				return false;
			}

			if(!$is_my_ticket)
			{
				switch ($status)
				{
					case 'close':
						\dash\log::set('setCloseTicket', ['code' => \dash\request::get('id')]);
						\dash\permission::access('supportTicketClose');
						break;

					case 'awaiting':
						\dash\log::set('setAwaitingTicket', ['code' => \dash\request::get('id')]);
						\dash\permission::access('supportTicketReOpen');
						break;

					case 'deleted':
						\dash\log::set('setDeleteTicket', ['code' => \dash\request::get('id')]);
						\dash\permission::access('supportTicketDelete');
						break;

				}
			}

			\dash\db\comments::update(['status' => $status], \dash\request::get('id'));

			switch ($status)
			{
				case 'close':
					\dash\notif::warn(T_("Ticket closed"));
					break;

				case 'awaiting':
					\dash\notif::ok(T_("Ticket was open again"));
					break;

				case 'deleted':
					\dash\notif::warn(T_("Ticket was deleted"));
					break;
			}

			\dash\redirect::pwd();

			return true;
		}


		if(\dash\request::post('TicketFormSolved') === 'solvedForm')
		{
			\dash\permission::access('supportTicketAnswer');

			$solved = \dash\request::post('solved') ? 1 : null;

			\dash\db\comments::update(['solved' => $solved], \dash\request::get('id'));
			if($solved)
			{
				\dash\log::set('setSolvedTicket', ['code' => \dash\request::get('id')]);
				\dash\notif::ok(T_("Ticket set as solved"));
			}
			else
			{
				\dash\log::set('setUnSolvedTicket', ['code' => \dash\request::get('id')]);
				\dash\notif::warn(T_("Ticket set as unsolved"));
			}
			\dash\redirect::pwd();

			return true;
		}

		$msg      = T_("Your ticket was sended");
		$notif_fn = 'ok';

		$ticket_type = 'ticket';
		if(\dash\permission::check('supportTicketAddNote'))
		{
			if(\dash\request::post('addnote') === 'note')
			{
				\dash\log::set('AddNoteTicket', ['code' => \dash\request::get('id')]);
				$msg = T_("Your note saved");
				$notif_fn = 'warn';
				$ticket_type = 'ticket_note';
			}
		}

		if(\dash\permission::check('supportTicketSignature'))
		{
			$content = \dash\request::post('content') ? $_POST['content'] : null;

			if((mb_strlen($content) - 1) === (mb_strlen(\dash\user::detail('signature'))))
			{
				$content = null;
			}
		}
		else
		{
			$content = \dash\request::post('content');
		}

		// ready to insert comments
		$args =
		[
			'author'  => \dash\user::detail('displayname'),
			'email'   => \dash\user::detail('email'),
			'type'    => $ticket_type,
			'content' => $content,
			'title'   => \dash\request::post('title'),
			'mobile'  => \dash\user::detail("mobile"),
			'user_id' => \dash\user::id(),
			'parent'  => \dash\request::get('id'),
			'file'    => $file,
		];


		$update_main = [];

		if($ticket_type !== 'ticket_note')
		{

			$plus = \dash\db\comments::get_count(['type' => 'ticket', 'parent' => \dash\request::get('id')]);

			$update_main['plus'] = intval($plus) + 1 + 1 ; // master ticket + this tichet

			if(!\dash\temp::get('ticketGuest'))
			{

				if($is_my_ticket)
				{

					$update_main['status'] = 'awaiting';
					$msg = T_("Your message has been added");
					$notif_fn = 'info';
				}
				else
				{

					\dash\permission::access('supportTicketAnswer');

					if(array_key_exists('subdomain', $main))
					{
						if($main['subdomain'] != \dash\url::subdomain())
						{
							\dash\permission::access('supportTicketManageSubdomain');
						}
					}

					\dash\log::set('AnswerTicket', ['code' => \dash\request::get('id')]);
					$update_main['status'] = 'answered';
					$msg = T_("Your answer was saved");

					if(isset($main['answertime']) && $main['answertime'])
					{
						// no change
					}
					else
					{
						if(isset($main['datecreated']))
						{
							$diff                      = time() - strtotime($main['datecreated']);
							$update_main['answertime'] = $diff;
						}
					}
				}
			}
		}

		$result = \dash\app\ticket::add($args);

		if($result)
		{
			if(!empty($update_main))
			{
				\dash\db\comments::update($update_main, \dash\request::get('id'));
			}

			\dash\notif::$notif_fn($msg);
			\dash\redirect::pwd();
		}
	}
}
?>