<?php
namespace dash\app;


class ref
{

	public static function save_ref()
	{
		// it the user is not login and use from ref in url
		// plus click ref of the referer user
		if(\dash\request::get("ref") && !\dash\user::login())
		{
			$url_ref = \dash\request::get('ref');
			$url_ref = \dash\coding::decode($url_ref);

			if(!$url_ref)
			{
				// invalid ref
				// fake ref
				return;
			}
			// plus the referer counter click
			$plus_counter_click = false;

			$log_meta =
			[
				'data' => $url_ref,
				'meta' =>
				[
					'url'     => \dash\url::directory(),
					'ref'     => \dash\request::get(),
					'session' => $_SESSION,
				],
			];

			if(isset($_SESSION['ref']))
			{
				if(intval($_SESSION['ref']) === intval($url_ref))
				{
					// user pres the F5 :|
					// neeed less to plus the click counter
					$plus_counter_click = false;
				}
				else
				{
					// user change the ref
					\dash\db\logs::set('user:ref:changed', null, $log_meta);
					$plus_counter_click = true;
				}
			}
			else
			{
				$plus_counter_click = true;
			}

			$_SESSION['ref'] = $url_ref;

			if($plus_counter_click)
			{
				$check_user_exist = \dash\db\users::get(['id' => $url_ref, 'limit' => 1]);
				if(isset($check_user_exist['id']))
				{
					$where =
					[
						'user_id' => $check_user_exist['id'],
						'key'     => 'user_ref_'. (string) $check_user_exist['id'],
					];
					\dash\db\options::plus($where);
				}
				else
				{
					unset($_SESSION['ref']);
					\dash\db\logs::set('user:ref:referer:not:exist', null, $log_meta);
				}
			}
		}
	}
}
?>