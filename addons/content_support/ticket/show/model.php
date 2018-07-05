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

		// ready to insert comments
		$args =
		[
			'author'  => \dash\user::detail('displayname'),
			'email'   => \dash\user::detail('email'),
			'type'    => 'ticket',
			'content' => \dash\request::post('desc'),
			'title'   => \dash\request::post('title'),
			'mobile'  => \dash\user::detail("mobile"),
			'user_id' => \dash\user::id(),
			'parent'  => \dash\request::get('id'),
			'file'    => $file,
		];

		$main = \dash\app\comment::get(\dash\request::get('id'));
		if(!$main || !isset($main['user_id']))
		{
			\dash\header::status(403, T_("Ticket not found"));
		}

		$ticket_user_id = $main['user_id'];
		$ticket_user_id = \dash\coding::decode($ticket_user_id);
		if(!$ticket_user_id)
		{
			\dash\header::status(403, T_("Ticket not found"));
		}

		$update_main = [];

		$plus = \dash\db\comments::get_count(['type' => 'ticket', 'parent' => \dash\coding::decode(\dash\request::get('id'))]);

		$update_main['plus'] = intval($plus) + 1 + 1 ; // master ticket + this tichet

		if(intval($ticket_user_id) === intval(\dash\user::id()))
		{
			$update_main['status'] = 'awaiting';
		}
		else
		{
			\dash\permission::access('supportTicketView');

			$update_main['status'] = 'answered';

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

		$result = \dash\app\comment::add($args);

		if($result)
		{
			if(!empty($update_main))
			{
				\dash\db\comments::update($update_main, \dash\coding::decode(\dash\request::get('id')));
			}

			\dash\notif::ok(T_("Your ticket was sended"));
			\dash\redirect::pwd();
		}
	}
}
?>