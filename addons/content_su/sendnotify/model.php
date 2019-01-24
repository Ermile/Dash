<?php
namespace content_su\sendnotify;


class model
{
	/**
	 * find connection to send notify to this users
	 *
	 * @param      <type>  $_mobile_id  The mobile identifier
	 */
	public static function connection_way($_mobile_id)
	{
		$data = [];
		$is_mobile = \dash\utility\filter::mobile($_mobile_id);
		if($is_mobile)
		{
			$data = \dash\db\users::get_by_mobile($is_mobile);
		}
		else
		{
			if(is_numeric($_mobile_id))
			{
				$data = \dash\db\users::get(['id' => $_mobile_id, 'limit' => 1]);
			}
		}

		if(empty($data))
		{
			\dash\notif::error(T_("User not found"));
			return false;
		}

		$way  = [];
		$info = [];
		if(isset($data['mobile']) && \dash\utility\filter::mobile($data['mobile']))		$way['mobile'] = $data['mobile'];
		if(isset($data['email']))			$way['email']        = $data['email'];
		if(isset($data['googlemail']))		$way['googlemail']   = $data['googlemail'];
		if(isset($data['chatid']))			$way['telegram']     = $data['chatid'];
		if(isset($data['facebookmail']))	$way['facebookmail'] = $data['facebookmail'];
		if(isset($data['twittermail']))		$way['twittermail']  = $data['twittermail'];

		if(isset($data['displayname']))		$info['displayname'] = $data['displayname'];
		if(isset($data['name']))			$info['name']        = $data['name'];
		if(isset($data['lastname']))		$info['lastname']    = $data['lastname'];
		if(isset($data['fileurl']))			$info['fileurl']     = $data['fileurl'];
		if(isset($data['status']))			$info['status']      = $data['status'];
		if(isset($data['setup']))			$info['setup']       = $data['setup'];

		$return            = [];
		$return['user_id'] = isset($data['id']) ? $data['id'] : null;
		$return['way']     = $way;
		$return['info']    = $info;
		return $return;

	}


	public static function post()
	{
		$msg = \dash\request::post('msg');
		if(!$msg)
		{
			\dash\notif::error(T_("No message was sended"));
			return false;
		}
		$user         = \dash\request::get('user');
		$detail       = self::connection_way($user);
		$email        = (\dash\request::post('email') && isset($detail['way']['email'])) 					? $detail['way']['email'] 			: null;
		$googlemail   = (\dash\request::post('googlemail') && isset($detail['way']['googlemail'])) 		? $detail['way']['googlemail'] 		: null;
		$telegram     = (\dash\request::post('telegram') && isset($detail['way']['telegram'])) 			? $detail['way']['telegram'] 		: null;
		$facebookmail = (\dash\request::post('facebookmail') && isset($detail['way']['facebookmail'])) 	? $detail['way']['facebookmail'] 	: null;
		$twittermail  = (\dash\request::post('twittermail') && isset($detail['way']['twittermail'])) 		? $detail['way']['twittermail'] 	: null;
		$notification = (\dash\request::post('notification')) ? true : false;
		$mobile       = (\dash\request::post('mobile') && isset($detail['way']['mobile'])) 				? $detail['way']['mobile'] 			: null;
		$user_id      = $detail['user_id'];

		\dash\log::set('sendnotify', ['code' => $user_id ]);

		if($notification && $user_id)
		{
	        // $this->send_notification(['text' => $msg, 'cat' => 'supervisor', 'to' => $user_id]);
	        // \dash\notif::ok(T_("Inner notification was sended"));
		}

		if($mobile)
		{
			\dash\utility\sms::send_array($mobile, $msg);
			\dash\notif::ok("SMS was sended");
		}

		if($telegram)
		{
			// \dash\utility\telegram::sendMessage($telegram, $msg);
			\dash\notif::ok("Telegram was sended");
		}
	}
}
?>
