<?php
namespace content_crm\member\glance;


class view
{
	public static function config()
	{
		\content_crm\member\main\view::dataRowMember();

		\dash\data::page_title(T_('Glance user'));
		\dash\data::page_desc(' ');
		\dash\data::page_pictogram('user');

		$user_code = \dash\data::dataRowMember_id();
		$user_id   = \dash\coding::decode($user_code);

		if(\dash\permission::supervisor() && \dash\request::get('showlog'))
		{

			\dash\data::showUserLog(\dash\app\user::user_in_all_table($user_id));
		}

		if($user_id)
		{
			$user_telegram = \dash\db\user_telegram::get(['user_id' => $user_id]);
			\dash\data::userTelegram($user_telegram);

			$user_android = \dash\db\user_android::get(['user_id' => $user_id]);
			\dash\data::userAndroid($user_android);
		}
	}
}
?>