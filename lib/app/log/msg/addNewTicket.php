<?php
namespace dash\app\log\msg;

class addNewTicket
{

	public static function msg($_args, $_user)
	{
		// $tg_msg                      = "🆔#Ticket|code #New \n🗣 ;displayname #user|user_code\n—————\n📬 :ttitle\n:tcontent\n:file\n⏳ |longdatecreated";

		if(isset($_user['displayname']))
		{
			$displayname = $_user['displayname'];
		}
		else
		{
			$displayname = T_("Unknow");
		}

		if(isset($_user['id']))
		{
			$user_code = $_user['id'];
			$user_code = \dash\coding::encode($user_code);
		}
		else
		{
			$user_code = null;
		}

		$data = isset($_args['data']) ? $_args['data'] : [];

		if(is_string($data) && (substr($data, 0, 1) === '{' || substr($data, 0, 1) === '['))
		{
			$data = json_decode($data, true);
		}

		$ttitle   = isset($data['ttitle']) ? $data['ttitle'] : null;
		$tcontent = isset($data['tcontent']) ? $data['tcontent'] : null;
		$file     = isset($data['file']) ? $data['file'] : null;

		$msg                = [];
		$msg['title']       = T_("Add new ticket");
		$msg['content']     = T_(":val add new ticket", ['val' => $displayname]);

		$msg['telegram']    = true;
		$msg['need_answer'] = true;

		$tg_msg = '';
		$tg_msg .= "🆔#Ticket".(isset($_args['code']) ? $_args['code']: null);
		$tg_msg .= " #New \n🗣 ". $displayname. " #user". $user_code;
		$tg_msg .= "\n—————\n📬 ";

		if($ttitle)
		{
			$tg_msg .= $ttitle . "\n";
		}

		if($tcontent)
		{
			$tcontent = \dash\app\log\msg::myStripTags($tcontent);
			$tg_msg .= $tcontent . "\n";
		}

		if($file)
		{
			$tg_msg .= $file . "\n";
		}

		if(isset($_args['datecreated']))
		{
			$tg_msg .= "\n⏳ ". \dash\datetime::fit($_args['datecreated'], true);
		}


		$msg['send_msg']             = [];
		$msg['send_msg']['telegram'] = $tg_msg;

		$msg['send_to']              = ['supervisor'];

		$msg['btn']                  = [];
		$msg['btn']['telegram']      =
		[
			'reply_markup'           =>
			[
				'keyboard'           =>
				[
					['/TicketAnswer |id'],
				],
				'one_time_keyboard'  => true,
			],
		];

		return $msg;
	}
}
?>