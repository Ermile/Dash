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
		if($_args['debug']) \lib\notif::title(T_("Operation Faild"));

		// set the log meta
		$log_meta =
		[
			'data' => null,
			'meta' =>
			[
				'user_id' => $this->user_id,
				'input'   => \lib\utility::request(),
			]
		];

		// check user id is exist
		if(!$this->user_id)
		{
			if($_args['save_log']) \lib\db\logs::set('addon:api:user:user_id:notfound', $this->user_id, $log_meta);
			if($_args['debug']) \lib\notif::error(T_("User not found"), 'user', 'permission');
			return false;
		}


		$mobile           = null;
		$mobile_syntax    = null;


		// get mobile of user
		$mobile           = \lib\utility::request("mobile");
		$mobile_syntax    = \lib\utility\filter::mobile($mobile);

		if($mobile && !$mobile_syntax)
		{
			if($_args['save_log']) \lib\db\logs::set('addon:api:user:mobile:not:set', $this->user_id, $log_meta);
			if($_args['debug']) \lib\notif::error(T_("Invalid mobile number"), 'mobile', 'arguments');
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

			$check_duplicate = \lib\db\users::get($check_duplicate);

			if(isset($check_duplicate['id']))
			{
				if($_args['method'] === 'post')
				{
					if($_args['save_log']) \lib\db\logs::set('addon:api:user:mobile:duplicate', $this->user_id, $log_meta);
					if($_args['debug']) \lib\notif::error(T_("Duplicate mobile"), 'mobile', 'arguments');
					return false;
				}
				else
				{
					$id = \lib\utility::request('id');
					$id = \lib\utility\shortURL::decode($id);
					if(intval($id) === intval($check_duplicate['id']))
					{
						// no problem this is current user
					}
					else
					{
						if($_args['save_log']) \lib\db\logs::set('addon:api:user:mobile:duplicate:update', $this->user_id, $log_meta);
						if($_args['debug']) \lib\notif::error(T_("Duplicate mobile"), 'mobile', 'arguments');
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

		if(!\lib\notif::$status || $return_function === false)
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

			\lib\db\users::insert($args);
		}
		elseif($_args['method'] === 'patch')
		{

			$id = \lib\utility::request('id');
			$id = \lib\utility\shortURL::decode($id);
			if(!$id)
			{
				if($_args['save_log']) \lib\db\logs::set('addon:api:user:pathc:id:not:set', $this->user_id, $log_meta);
				if($_args['debug']) \lib\notif::error(T_("Id not set"), 'id', 'arguments');
				return false;
			}

			if($_args['meta'] && is_array($_args['meta']))
			{
				$current_meta = \lib\db\users::get(['id' => $id, 'limit' => 1]);

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

			if(!\lib\utility::isset_request('mobile'))              unset($args['mobile']);
			if(!\lib\utility::isset_request('passportexpire'))      unset($args['passportexpire']);
			if(!\lib\utility::isset_request('postion'))             unset($args['postion']);
			if(!\lib\utility::isset_request('personnelcode'))       unset($args['personnelcode']);
			if(!\lib\utility::isset_request('firstname'))           unset($args['name']);
			if(!\lib\utility::isset_request('lastname'))            unset($args['lastname']);
			if(!\lib\utility::isset_request('status'))              unset($args['status']);
			if(!\lib\utility::isset_request('displayname'))         unset($args['displayname']);
			if(!\lib\utility::isset_request('nationalcode'))        unset($args['nationalcode']);
			if(!\lib\utility::isset_request('father'))              unset($args['father']);
			if(!\lib\utility::isset_request('birthday'))            unset($args['birthday']);
			if(!\lib\utility::isset_request('gender'))              unset($args['gender']);
			if(!\lib\utility::isset_request('type'))                unset($args['type']);
			if(!\lib\utility::isset_request('marital'))             unset($args['marital']);
			if(!\lib\utility::isset_request('child'))               unset($args['childcount']);
			if(!\lib\utility::isset_request('birthplace'))          unset($args['birthplace']);
			if(!\lib\utility::isset_request('shfrom'))              unset($args['shfrom']);
			if(!\lib\utility::isset_request('shcode'))              unset($args['shcode']);
			if(!\lib\utility::isset_request('education'))           unset($args['education']);
			if(!\lib\utility::isset_request('job'))                 unset($args['job']);
			if(!\lib\utility::isset_request('passportcode'))        unset($args['passportcode']);
			if(!\lib\utility::isset_request('passportcode'))        unset($args['pasportcode']);
			if(!\lib\utility::isset_request('paymentaccountnumber'))unset($args['cardnumber']);
			if(!\lib\utility::isset_request('paymentaccountnumber'))unset($args['paymentaccountnumber']);
			if(!\lib\utility::isset_request('shaba'))               unset($args['shaba']);
			if(!\lib\utility::isset_request('file'))                unset($args['fileid'], $args['fileurl']);
			if(!\lib\utility::isset_request('email'))               unset($args['email']);
			if(!\lib\utility::isset_request('parent'))              unset($args['parent']);
			if(!\lib\utility::isset_request('permission'))          unset($args['permission']);
			if(!\lib\utility::isset_request('username'))            unset($args['username']);
			if(!\lib\utility::isset_request('group'))               unset($args['group']);
			if(!\lib\utility::isset_request('pin'))                 unset($args['pin']);
			if(!\lib\utility::isset_request('ref'))                 unset($args['ref']);
			if(!\lib\utility::isset_request('notification'))        unset($args['notification']);
			if(!\lib\utility::isset_request('nationality'))         unset($args['nationality']);
			if(!\lib\utility::isset_request('region'))              unset($args['region']);
			if(!\lib\utility::isset_request('insurancetype'))       unset($args['insurancetype']);
			if(!\lib\utility::isset_request('insurancecode'))       unset($args['insurancecode']);
			if(!\lib\utility::isset_request('dependantscount'))     unset($args['dependantscount']);
			if(!\lib\utility::isset_request('unit_id'))             unset($args['unit_id']);
			if(!\lib\utility::isset_request('language'))            unset($args['language']);
			if(!\lib\utility::isset_request('twostep'))             unset($args['twostep']);
			if(!\lib\utility::isset_request('setup'))               unset($args['setup']);

			if(!empty($args))
			{
				\lib\db\users::update($args, $id);
			}
		}

		$return = [];

		if(\lib\notif::$status)
		{
			if($_args['debug']) \lib\notif::title(T_("Operation Complete"));

			if($_args['method'] === 'post')
			{
				if($_args['debug']) \lib\notif::true(T_("user successfully added"));
				$return['user_id'] = \lib\utility\shortURL::encode(\lib\db::insert_id());
			}
			elseif($_args['method'] === 'patch')
			{
				if($_args['debug']) \lib\notif::true(T_("user successfully updated"));
				$return['user_id'] = \lib\utility::request('id');
			}
		}

		return $return;
	}
}
?>