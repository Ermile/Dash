<?php
namespace content_support\message\edit;

class model
{




	public static function post()
	{
		\dash\permission::check('supportEditMessage');

		if(\dash\permission::supervisor() && \dash\request::post('removeMessage'))
		{
			\dash\log::set('supportMessageDELETED', ['code' => \dash\request::post('parent'), 'message_id' => \dash\request::get('id')]);
			\dash\db\tickets::hard_delete(\dash\request::get('id'));
			if(\dash\request::post('parent'))
			{
				\dash\redirect::to(\dash\url::here().'/ticket/show?id='. \dash\request::post('parent'));
			}
			else
			{
				\dash\redirect::to(\dash\url::here().'/ticket');
			}
		}

		// ready to insert tickets
		$content = \dash\request::post('content') ? $_POST['content'] : null;
		if(!trim($content))
		{
			\dash\notif::error(T_("Please fill the content"));
			return false;
		}

		$args =
		[
			'content' => \dash\safe::safe($content, 'raw'),
		];

		\content_support\message\edit\view::config();

		\dash\db\tickets::update($args, \dash\request::get('id'));

		\dash\log::set('supportMessageEdit', ['code' => \dash\request::get('id')]);

		\dash\notif::ok(T_("Ticket updated"));
		\dash\redirect::to(\dash\url::here().'/ticket/show?id='. \dash\request::get('id'));

	}
}
?>