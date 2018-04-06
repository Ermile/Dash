<?php
namespace addons\content_api\v1\parent\tools;


trait delete
{

	public function parent_cancel_request($_args = [])
	{
		$default_args =
		[
			'method' => 'put'
		];

		if(!is_array($_args))
		{
			$_args = [];
		}

		$_args = array_merge($default_args, $_args);


		$log_meta =
		[
			'data' => null,
			'meta' =>
			[
				'input' => \dash\utility::request(),
			]
		];

		if(!$this->user_id)
		{
			\dash\db\logs::set('api:parent:remove:request:user_id:notfound', null, $log_meta);
			\dash\notif::error(T_("User not found"), 'user', 'permission');
			return false;
		}

		$notify_id = \dash\utility::request('id');

		$notify_id = \dash\coding::decode($notify_id);
		if(!$notify_id)
		{
			\dash\db\logs::set('api:parent:remove:request:notify:id:not:set', $this->user_id, $log_meta);
			\dash\notif::error(T_("Invalid request id"));
			return false;
		}


		$get_notify =
		[
			'id'              => $notify_id,
			'user_idsender'   => $this->user_id,
			'category'        => 9,
			'status'          => 'enable',
			'related_id'      => $this->user_id,
			'related_foreign' => 'users',
			'needanswer'      => 1,
			'answer'          => null,
			'limit'           => 1,
		];

		$check_ok = \dash\db\notifications::get($get_notify);
		if(!$check_ok)
		{
			\dash\db\logs::set('api:parent:remove:request:notify:data:invalid:access', $this->user_id, $log_meta);
			\dash\notif::error(T_("Invalid request data"));
			return false;
		}

		\dash\db\notifications::update(['status' => 'cancel'], $notify_id);
		if(\dash\engine\process::status())
		{
			\dash\db\logs::set('api:parent:remove:request:sucsessful', $this->user_id, $log_meta);
			\dash\notif::ok(T_("Your request canceled"));
		}

	}


	/**
	 * delete the parent
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public function delete_parent($_args = [])
	{
		$default_args =
		[
			'method' => 'put'
		];

		if(!is_array($_args))
		{
			$_args = [];
		}

		$_args = array_merge($default_args, $_args);


		$log_meta =
		[
			'data' => null,
			'meta' =>
			[
				'input' => \dash\utility::request(),
			]
		];

		if(!$this->user_id)
		{
			\dash\db\logs::set('api:parent:delete:user_id:notfound', null, $log_meta);
			\dash\notif::error(T_("User not found"), 'user', 'permission');
			return false;
		}

		$userparents_id = \dash\utility::request('id');
		$userparents_id = \dash\coding::decode($userparents_id);
		if(!$userparents_id)
		{
			\dash\db\logs::set('api:parent:delete:notify:data:invalid:access', $this->user_id, $log_meta);
			\dash\notif::error(T_("Invalid remove data"));
			return false;
		}

		$related_id = \dash\utility::request('related_id');
		$related_id = \dash\coding::decode($related_id);
		if(!$userparents_id && \dash\utility::request('related_id'))
		{
			\dash\db\logs::set('api:parent:delete:related_id:invalid', $this->user_id, $log_meta);
			\dash\notif::error(T_("Invalid remove data"));
			return false;
		}

		if($related_id)
		{
			$get =
			[
				'id'         => $userparents_id,
				'related_id' => $related_id,
				'limit'      => 1,
			];
		}
		else
		{
			$get =
			[
				'id'         => $userparents_id,
				'user_id'    => $this->user_id,
				'related_id' => null,
				'limit'      => 1,
			];
		}

		$check = \dash\db\userparents::get($get);
		if(!isset($check['id']))
		{
			\dash\db\logs::set('api:parent:delete:notify:data:invalid:access:id', $this->user_id, $log_meta);
			\dash\notif::error(T_("Invalid remove details"));
			return false;
		}

		\dash\db\userparents::update(['status' => 'deleted'], $userparents_id);
		if(\dash\engine\process::status())
		{
			\dash\db\logs::set('api:parent:delete:sucsessful', $this->user_id, $log_meta);
			\dash\notif::ok(T_("Parent removed"));
		}
	}
}
?>