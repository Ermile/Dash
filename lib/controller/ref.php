<?php
namespace lib\controller;


trait ref
{
	/**
	 * Saves a reference.
	 *
	 */
	public function save_ref()
	{
		// it the user is not login and use from ref in url
		// plus click ref of the referer user
		if(\lib\request::get("ref") && !$this->login())
		{
			$url_ref = \lib\request::get('ref');
			$url_ref = \lib\utility\shortURL::decode($url_ref);

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
					'url'     => \lib\url::directory(),
					'ref'     => \lib\request::get(),
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
					\lib\db\logs::set('user:ref:changed', null, $log_meta);
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
				$check_user_exist = \lib\db\users::get(['id' => $url_ref, 'limit' => 1]);
				if(isset($check_user_exist['id']))
				{
					$where =
					[
						'user_id' => $check_user_exist['id'],
						'key'     => 'user_ref_'. (string) $check_user_exist['id'],
					];
					\lib\db\options::plus($where);
				}
				else
				{
					unset($_SESSION['ref']);
					\lib\db\logs::set('user:ref:referer:not:exist', null, $log_meta);
				}
			}
		}
	}
}
?>