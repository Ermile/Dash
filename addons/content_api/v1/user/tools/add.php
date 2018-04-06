<?php
namespace addons\content_api\v1\user\tools;


trait add
{

	use user_check_args;
	/**
	 * Adds a user.
	 *
	 * @param      array    $_args  The arguments
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public function add_user($_args = [])
	{

		// ready to insert userteam or userbranch record
		$args                  = [];

		// default args
		$default_args =
		[
			'method'   => 'post',
			'debug'    => true,
			'save_log' => true,
			// save somting in meta
			'meta'     => null,
		];

		if(!is_array($_args))
		{
			$_args = [];
		}
		// merge default args and args
		$_args = array_merge($default_args, $_args);

		// set default title of debug
		// if($_args['debug']) // \dash\notif::title(T_("Operation Faild"));

		// set the log meta
		$log_meta =
		[
			'data' => null,
			'meta' =>
			[
				'user_id' => $this->user_id,
				'input'   => \dash\utility::request(),
			]
		];

		// check user id is exist
		if(!$this->user_id)
		{
			if($_args['save_log']) \dash\db\logs::set('addon:api:user:user_id:notfound', $this->user_id, $log_meta);
			if($_args['debug']) \dash\notif::error(T_("User not found"), 'user', 'permission');
			return false;
		}


		$mobile           = null;
		$mobile_syntax    = null;


		// get mobile of user
		$mobile           = \dash\utility::request("mobile");
		$mobile_syntax    = \dash\utility\filter::mobile($mobile);

		if($mobile && !$mobile_syntax)
		{
			if($_args['save_log']) \dash\db\logs::set('addon:api:user:mobile:not:set', $this->user_id, $log_meta);
			if($_args['debug']) \dash\notif::error(T_("Invalid mobile number"), 'mobile', 'arguments');
			return false;
		}
		elseif($mobile && $mobile_syntax && ctype_digit($mobile))
		{
			$mobile = $mobile_syntax;
		}
		else
		{
			$mobile_syntax = $mobile = null;
		}

		if($mobile)
		{
			$check_duplicate =
			[
				'mobile' => $mobile,
				'status' => ["IN", "('active', 'awaiting')"],
				'limit'  => 1,
			];

			$check_duplicate = \dash\db\users::get($check_duplicate);

			if(isset($check_duplicate['id']))
			{
				if($_args['method'] === 'post')
				{
					if($_args['save_log']) \dash\db\logs::set('addon:api:user:mobile:duplicate', $this->user_id, $log_meta);
					if($_args['debug']) \dash\notif::error(T_("Duplicate mobile"), 'mobile', 'arguments');
					return false;
				}
				else
				{
					$id = \dash\utility::request('id');
					$id = \dash\coding::decode($id);
					if(intval($id) === intval($check_duplicate['id']))
					{
						// no problem this is current user
					}
					else
					{
						if($_args['save_log']) \dash\db\logs::set('addon:api:user:mobile:duplicate:update', $this->user_id, $log_meta);
						if($_args['debug']) \dash\notif::error(T_("Duplicate mobile"), 'mobile', 'arguments');
						return false;
					}
				}
			}
		}

		$args['mobile'] = $mobile;

		/**
		 * check and set the args
		 */
		$return_function = $this->user_check_args($_args, $args, $log_meta);

		if(!\dash\engine\process::status() || $return_function === false)
		{
			return false;
		}

		// insert new user team
		if($_args['method'] === 'post')
		{
			if($_args['meta'] && is_array($_args['meta']))
			{
				$args['meta'] = json_encode($_args['meta'], JSON_UNESCAPED_UNICODE);
			}

			\dash\db\users::insert($args);
		}
		elseif($_args['method'] === 'patch')
		{

			$id = \dash\utility::request('id');
			$id = \dash\coding::decode($id);
			if(!$id)
			{
				if($_args['save_log']) \dash\db\logs::set('addon:api:user:pathc:id:not:set', $this->user_id, $log_meta);
				if($_args['debug']) \dash\notif::error(T_("Id not set"), 'id', 'arguments');
				return false;
			}

			if($_args['meta'] && is_array($_args['meta']))
			{
				$current_meta = \dash\db\users::get(['id' => $id, 'limit' => 1]);

				if(isset($current_meta['meta']))
				{
					if(is_string($current_meta['meta']) && substr($current_meta['meta'], 0, 1) === '{')
					{
						$current_meta['meta'] = json_decode($current_meta['meta'], true);
					}

					if(is_array($current_meta['meta']))
					{
						$args['meta'] = json_encode(array_merge($current_meta['meta'], $_args['meta']), JSON_UNESCAPED_UNICODE);
					}
					else
					{
						$args['meta'] = json_encode($_args['meta'], JSON_UNESCAPED_UNICODE);
					}
				}
				else
				{
					$args['meta'] = json_encode($_args['meta'], JSON_UNESCAPED_UNICODE);
				}
			}

			if(!\dash\utility::isset_request('mobile'))              unset($args['mobile']);
			if(!\dash\utility::isset_request('passportexpire'))      unset($args['passportexpire']);
			if(!\dash\utility::isset_request('postion'))             unset($args['postion']);
			if(!\dash\utility::isset_request('personnelcode'))       unset($args['personnelcode']);
			if(!\dash\utility::isset_request('firstname'))           unset($args['name']);
			if(!\dash\utility::isset_request('lastname'))            unset($args['lastname']);
			if(!\dash\utility::isset_request('status'))              unset($args['status']);
			if(!\dash\utility::isset_request('displayname'))         unset($args['displayname']);
			if(!\dash\utility::isset_request('nationalcode'))        unset($args['nationalcode']);
			if(!\dash\utility::isset_request('father'))              unset($args['father']);
			if(!\dash\utility::isset_request('birthday'))            unset($args['birthday']);
			if(!\dash\utility::isset_request('gender'))              unset($args['gender']);
			if(!\dash\utility::isset_request('type'))                unset($args['type']);
			if(!\dash\utility::isset_request('marital'))             unset($args['marital']);
			if(!\dash\utility::isset_request('child'))               unset($args['childcount']);
			if(!\dash\utility::isset_request('birthplace'))          unset($args['birthplace']);
			if(!\dash\utility::isset_request('shfrom'))              unset($args['shfrom']);
			if(!\dash\utility::isset_request('shcode'))              unset($args['shcode']);
			if(!\dash\utility::isset_request('education'))           unset($args['education']);
			if(!\dash\utility::isset_request('job'))                 unset($args['job']);
			if(!\dash\utility::isset_request('passportcode'))        unset($args['passportcode']);
			if(!\dash\utility::isset_request('passportcode'))        unset($args['pasportcode']);
			if(!\dash\utility::isset_request('paymentaccountnumber'))unset($args['cardnumber']);
			if(!\dash\utility::isset_request('paymentaccountnumber'))unset($args['paymentaccountnumber']);
			if(!\dash\utility::isset_request('shaba'))               unset($args['shaba']);
			if(!\dash\utility::isset_request('file'))                unset($args['fileid'], $args['fileurl']);
			if(!\dash\utility::isset_request('email'))               unset($args['email']);
			if(!\dash\utility::isset_request('parent'))              unset($args['parent']);
			if(!\dash\utility::isset_request('permission'))          unset($args['permission']);
			if(!\dash\utility::isset_request('username'))            unset($args['username']);
			if(!\dash\utility::isset_request('group'))               unset($args['group']);
			if(!\dash\utility::isset_request('pin'))                 unset($args['pin']);
			if(!\dash\utility::isset_request('ref'))                 unset($args['ref']);
			if(!\dash\utility::isset_request('notification'))        unset($args['notification']);
			if(!\dash\utility::isset_request('nationality'))         unset($args['nationality']);
			if(!\dash\utility::isset_request('region'))              unset($args['region']);
			if(!\dash\utility::isset_request('insurancetype'))       unset($args['insurancetype']);
			if(!\dash\utility::isset_request('insurancecode'))       unset($args['insurancecode']);
			if(!\dash\utility::isset_request('dependantscount'))     unset($args['dependantscount']);
			if(!\dash\utility::isset_request('unit_id'))             unset($args['unit_id']);
			if(!\dash\utility::isset_request('language'))            unset($args['language']);
			if(!\dash\utility::isset_request('twostep'))             unset($args['twostep']);
			if(!\dash\utility::isset_request('setup'))               unset($args['setup']);

			if(!empty($args))
			{
				\dash\db\users::update($args, $id);
			}
		}

		$return = [];

		if(\dash\engine\process::status())
		{
			// if($_args['debug']) // \dash\notif::title(T_("Operation Complete"));

			if($_args['method'] === 'post')
			{
				if($_args['debug']) \dash\notif::ok(T_("user successfully added"));
				$return['user_id'] = \dash\coding::encode(\dash\db::insert_id());
			}
			elseif($_args['method'] === 'patch')
			{
				if($_args['debug']) \dash\notif::ok(T_("user successfully updated"));
				$return['user_id'] = \dash\utility::request('id');
			}
		}

		return $return;
	}
}
?>