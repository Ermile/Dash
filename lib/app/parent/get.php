<?php
namespace dash\app\parent;


trait get
{

	public static function get_list_parent($_args = [])
	{
		$default_args =
		[
			'method' => 'get'
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
				'input' => \dash\app::request(),
			]
		];

		if(!\dash\user::id())
		{
			\dash\db\logs::set('api:parent:user_id:notfound', null, $log_meta);
			\dash\notif::error(T_("User not found"), 'user', 'permission');
			return false;
		}

		$user_id = \dash\app::request('id');
		$user_id = \dash\coding::decode($user_id);
		if(!$user_id)
		{
			\dash\db\logs::set('api:parent:user_id:is:incurrect', null, $log_meta);
			\dash\notif::error(T_("User not found"), 'user', 'permission');
			return false;
		}


		$related_id = \dash\app::request('related_id');
		$related_id = \dash\coding::decode($related_id);
		if(!$related_id && \dash\app::request('related_id'))
		{
			\dash\db\logs::set('api:parent:related_id:is:incurrect', null, $log_meta);
			\dash\notif::error(T_("Related id is incurrect"), 'related_id', 'permission');
			return false;
		}

		$result = [];
		// $get_notify =
		// [
		// 	'user_idsender'   => $user_id,
		// 	'category'        => 9,
		// 	'status'          => 'enable',
		// 	'related_id'      => $user_id,
		// 	'related_foreign' => 'users',
		// 	'needanswer'      => 1,
		// 	'answer'          => null,
		// ];

		// $notify_list = \dash\db\notifications::get($get_notify);
		$notify_list = null;
		if($notify_list && is_array($notify_list))
		{
			$notify_list = \dash\utility\filter::meta_decode($notify_list);
			foreach ($notify_list as $key => $value)
			{
				$temp                = [];

				$temp['msg']         = T_("Waiting to user accept this request");
				$temp['notify']      = true;

				$temp['id']          = isset($value['id'])? $value['id'] : null;

				$temp['fileurl']    = isset($value['meta']['parent_fileurl'])? $value['meta']['parent_fileurl'] : null;
				$temp['mobile']      = isset($value['meta']['parent_mobile'])? $value['meta']['parent_mobile'] : null;
				$temp['displayname'] = isset($value['meta']['parent_displayname'])? $value['meta']['parent_displayname'] : null;
				$temp['title']       = isset($value['meta']['title'])? $value['meta']['title'] : null;
				$temp['othertitle']  = isset($value['meta']['othertitle'])? $value['meta']['othertitle'] : null;

				if($temp['title'] === 'custom' && $temp['othertitle'])
				{
					$temp['title'] = $temp['othertitle'];
					unset($temp['othertitle']);
				}

				$result[] = $temp;
			}
		}

		if($related_id)
		{
			$parent_resutl = \dash\db\userparents::load_parent(['user_id' => $user_id, 'status' => 'enable', 'related_id' => $related_id]);
		}
		else
		{
			$parent_resutl = \dash\db\userparents::load_parent(['user_id' => $user_id, 'status' => 'enable']);
		}
		if(is_array($parent_resutl))
		{
			foreach ($parent_resutl as $key => $value)
			{
				array_push($result, $value);
			}
		}
		if(is_array($result))
		{
			foreach ($result as $key => $value)
			{
				if(isset($value['fileurl']))
				{
					$result[$key]['fileurl'] = self::host('file'). '/'. $value['fileurl'];
				}
			}
		}

		return $result;
	}
}
?>