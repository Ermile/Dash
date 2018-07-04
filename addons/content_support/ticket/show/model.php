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

		if(intval($ticket_user_id) === intval(\dash\user::id()))
		{
			\dash\db\comments::update(['status' => 'awaiting'], \dash\coding::decode(\dash\request::get('id')));
		}
		else
		{
			\dash\db\comments::update(['status' => 'answered'], \dash\coding::decode(\dash\request::get('id')));
		}

		// insert comments
		$result = \dash\app\comment::add($args);

		if($result)
		{
			\dash\notif::ok(T_("Your ticket was sended"));
			\dash\redirect::pwd();
		}
	}
}
?>