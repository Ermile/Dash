<?php
namespace content_support\ticket\add;

class model
{

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
			// 'content' => \dash\request::post('content') ? $_POST['content'] : null,
			'title'   => \dash\request::post('title'),
			'mobile'  => \dash\user::detail("mobile"),
			'file'    => $file,
			'user_id' => \dash\user::id(),
		];

		if(\dash\permission::check('supportTicketSignature'))
		{
			$args['content'] = \dash\request::post('content') ? $_POST['content'] : null;
		}
		else
		{
			$args['content'] = \dash\request::post('content');
		}

		// insert comments
		$result = \dash\app\ticket::add($args);

		if(isset($result['id']))
		{
			$log =
			[
				'code'     => $result['id'],
				'ttitle'   => $args['title'],
				'tcontent' => \dash\safe::forJson($args['content']),
				'file'     => $args['file'] ? $args['file'] :"\n",
			];

			\dash\log::set('addNewTicket', $log);

			\dash\notif::ok(T_("Your ticket was sended"));
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