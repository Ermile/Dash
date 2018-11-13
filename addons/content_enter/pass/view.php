<?php
namespace content_enter\pass;


class view
{

	public static function config()
	{
		\dash\data::page_title(T_('Password'));
		\dash\data::page_desc(T_('Please enter password to enter'));

		switch (\dash\url::child())
		{
			case 'recovery':
				\dash\data::page_title(T_('Recovery Password'));
				\dash\data::page_desc(T_('If forget your password, Please enter new password. after pass verification your new password is usable.'));
				break;

			case 'signup':
			case 'set':
				\dash\data::page_title(T_('Set Password'));
				\dash\data::page_desc(T_('Please set your password to secure signup.'). ' '. T_('Next time we only need your mobile and this password to enter'));
				break;

			case 'change':
				\dash\data::page_title(T_('Change to new Password'));
				\dash\data::page_desc(T_('Please set new password to change it'));
				break;

			default:
				break;
		}
	}
}
?>