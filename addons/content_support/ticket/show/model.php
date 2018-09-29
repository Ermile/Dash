<?php
namespace content_support\ticket\show;

class model
{

	public static function post()
	{

		$id = \dash\request::get('id');

		if(\dash\request::post('TicketFormType') === 'tag')
		{
			if(self::save_tag($id, \dash\request::post('tag')))
			{
				\dash\redirect::pwd();
			}
			return false;
		}

		if(\dash\request::post('TicketFormType') === 'changeStatus')
		{
			if(self::change_status($id, \dash\request::post('status')))
			{
				\dash\redirect::pwd();
			}
			return false;
		}

		if(\dash\request::post('TicketFormSolved') === 'solvedForm')
		{
			if(self::save_solved($id, \dash\request::post('solved')))
			{
				\dash\redirect::pwd();
			}
			return false;
		}

		if(self::answer_save($id, \dash\request::post('content') ? $_POST['content'] : null, \dash\request::post('addnote')))
		{
			\dash\redirect::pwd();
		}

	}


	public static function save_tag($_id, $_tag)
	{
		\dash\permission::access('supportTicketAssignTag');

		if(!\dash\permission::check('cpTagSupportAdd'))
		{
			$current_tag = \dash\db\terms::get(['type' => 'support_tag']);
			if(is_array($current_tag))
			{
				$tag_titles = array_column($current_tag, 'title');
				$new_tag    = $_tag;
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

		\dash\app::variable(['support_tag' => $_tag]);
		\dash\app\posts::set_post_term($_id, 'support_tag', 'comments');
		\dash\log::set('ticketAddTag', ['code' => $_id, 'tag' => $_tag]);
		\dash\notif::ok(T_("Tag was saved"));
		return true;
	}



	public static function change_status($_id, $_status)
	{
		$main = \dash\app\ticket::get($_id);

		if(!$main || !array_key_exists('user_id', $main))
		{
			\dash\header::status(403, T_("Ticket not found"));
		}

		// check is my ticket and some permission to load guest , ...
		$is_my_ticket = \content_support\ticket\show\view::is_my_ticket($main);

		$status = $_status;

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
					$log =
					[
						'code' => $_id,

					];
					\dash\log::set('setCloseTicket', $log);

					\dash\permission::access('supportTicketClose');
					break;

				case 'awaiting':
					\dash\log::set('setAwaitingTicket', ['code' => $_id]);
					\dash\permission::access('supportTicketReOpen');
					break;

				case 'deleted':
					\dash\log::set('setDeleteTicket', ['code' => $_id]);
					\dash\permission::access('supportTicketDelete');
					break;

			}
		}

		\dash\db\comments::update(['status' => $status], $_id);

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

		return true;
	}


	public static function save_solved($_id, $_solved)
	{

		\dash\permission::access('supportTicketAnswer');

		$solved = $_solved ? 1 : null;

		\dash\db\comments::update(['solved' => $solved], $_id);
		if($solved)
		{
			\dash\log::set('setSolvedTicket', ['code' => $_id]);
			\dash\notif::ok(T_("Ticket set as solved"));
		}
		else
		{
			\dash\log::set('setUnSolvedTicket', ['code' => $_id]);
			\dash\notif::warn(T_("Ticket set as unsolved"));
		}

		return true;

	}

	public static function answer_save($_id, $_answer, $_type = 'ticket')
	{
		$file     = self::upload_file('file');

		// we have an error in upload file1
		if($file === false)
		{
			return false;
		}

		$main = \dash\app\ticket::get($_id);

		if(!$main || !array_key_exists('user_id', $main))
		{
			\dash\header::status(403, T_("Ticket not found"));
		}

		// check is my ticket and some permission to load guest , ...
		$is_my_ticket = \content_support\ticket\show\view::is_my_ticket($main);

		$msg      = T_("Your ticket was sended");
		$notif_fn = 'ok';


		$content = $_answer;

		if(\dash\permission::check('supportTicketSignature'))
		{

			if((mb_strlen($content) - 1) === (mb_strlen(\dash\user::detail('signature'))))
			{
				$content = null;
			}
		}

		$plus = \dash\db\comments::get_count(['type' => 'ticket', 'parent' => $_id]);

		$ticket_type = 'ticket';

		if(\dash\permission::check('supportTicketAddNote'))
		{
			if($_type === 'note')
			{
				$log =
				[
					'code'     => $_id,
					'tcontent' => $content,
					'file'     => $file ? $file :"\n",
					'plus'     => $plus,
				];

				\dash\log::set('AddNoteTicket', $log);

				$msg         = T_("Your note saved");
				$notif_fn    = 'warn';
				$ticket_type = 'ticket_note';
			}
		}


		// ready to insert comments
		$args =
		[
			'author'  => \dash\user::detail('displayname'),
			'email'   => \dash\user::detail('email'),
			'type'    => $ticket_type,
			'content' => $content,
			// 'title'   => \dash\request::post('title'),
			'mobile'  => \dash\user::detail("mobile"),
			'user_id' => \dash\user::id(),
			'parent'  => $_id,
			'file'    => $file,
		];


		$update_main = [];


		if($ticket_type !== 'ticket_note')
		{

			$update_main['plus'] = intval($plus) + 1 + 1 ; // master ticket + this tichet

			if(!\dash\temp::get('ticketGuest'))
			{

				if($is_my_ticket)
				{

					$update_main['status'] = 'awaiting';
					$msg = T_("Your message has been added");
					$notif_fn = 'info';

					$log =
					[
						'code'     => $_id,
						'tcontent' => $content,
						'file'     => $file ? $file :"\n",
						'plus'     => $update_main['plus'],
					];

					\dash\log::set('AddToTicket', $log);
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

					$log =
					[
						'code'     => $_id,
						'tcontent' => $content,
						'file'     => $file ? $file :"\n",
						'plus'     => $update_main['plus'],
					];

					\dash\log::set('AnswerTicket', $log);

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
				\dash\db\comments::update($update_main, $_id);
			}

			\dash\notif::$notif_fn($msg);
			return true;
		}
		return false;
	}



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

}
?>