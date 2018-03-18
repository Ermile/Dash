<?php
namespace addons\content_su\sendnotify;


class model extends \addons\content_su\main\model
{
	/**
	 * find connection to send notify to this users
	 *
	 * @param      <type>  $_mobile_id  The mobile identifier
	 */
	public function connection_way($_mobile_id)
	{
		$data = [];
		$is_mobile = \lib\utility\filter::mobile($_mobile_id);
		if($is_mobile)
		{
			$data = \lib\db\users::get_by_mobile($is_mobile);
		}
		else
		{
			if(is_numeric($_mobile_id))
			{
				$data = \lib\db\users::get(['id' => $_mobile_id, 'limit' => 1]);
			}
		}

		if(empty($data))
		{
			\lib\notif::error(T_("User not found"));
			return false;
		}

		$way  = [];
		$info = [];
		if(isset($data['mobile']) && \lib\utility\filter::mobile($data['mobile']))		$way['mobile'] = $data['mobile'];
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


	public function post_nofity($_args)
	{
		$msg = \lib\request::post('msg');
		if(!$msg)
		{
			\lib\notif::error(T_("No message was sended"));
			return false;
		}

		$user         = \lib\request::get('user');
		$detail       = $this->connection_way($user);
		$email        = (\lib\request::post('email') && isset($detail['way']['email'])) 					? $detail['way']['email'] 			: null;
		$googlemail   = (\lib\request::post('googlemail') && isset($detail['way']['googlemail'])) 		? $detail['way']['googlemail'] 		: null;
		$telegram     = (\lib\request::post('telegram') && isset($detail['way']['telegram'])) 			? $detail['way']['telegram'] 		: null;
		$facebookmail = (\lib\request::post('facebookmail') && isset($detail['way']['facebookmail'])) 	? $detail['way']['facebookmail'] 	: null;
		$twittermail  = (\lib\request::post('twittermail') && isset($detail['way']['twittermail'])) 		? $detail['way']['twittermail'] 	: null;
		$notification = (\lib\request::post('notification')) ? true : false;
		$mobile       = (\lib\request::post('mobile') && isset($detail['way']['mobile'])) 				? $detail['way']['mobile'] 			: null;
		$user_id      = $detail['user_id'];

		if($notification && $user_id)
		{
	        $this->send_notification(['text' => $msg, 'cat' => 'supervisor', 'to' => $user_id]);
	        \lib\notif::ok(T_("Inner notification was sended"));
		}

		if($mobile)
		{
			\lib\utility\sms::send_array($mobile, $msg);
			\lib\notif::ok("SMS was sended");
		}

		if($telegram)
		{
			\lib\utility\telegram::sendMessage($telegram, $msg);
			\lib\notif::ok("Telegram was sended");
		}
	}
}
?>
