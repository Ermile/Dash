<?php
namespace content_support;

class controller
{
	/**
	 * rout
	 */
	public static function routing()
	{
		if(!\dash\user::login())
		{
			$id    = \dash\request::get('id');
			$guest = \dash\request::get('guest');
			if(\dash\url::module() === 'ticket' && \dash\url::child() === 'show' && $id && $guest && is_numeric($id))
			{
				$load_id = \dash\db\tickets::get(['id' => $id, 'limit' => 1]);
				if(isset($load_id['datecreated']))
				{
					$md5 = (string) $id. '^_^-*_*'. $load_id['datecreated'];
					$md5 = md5($md5);

					if($md5 === $guest)
					{
						// no promble to load it
						\dash\temp::set('ticketGuest', true);
					}
					else
					{
						\dash\header::status(404, T_("Invalid token!"));
					}
				}
				else
				{
					\dash\header::status(404, T_("Invalid token!"));
				}

			}
			else
			{
				// \dash\redirect::to(\dash\url::kingdom(). '/enter');
				// return;
			}
		}

	}
}
?>