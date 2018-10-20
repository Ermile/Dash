<?php
namespace dash\app\log\msg;

class support
{

	public static function getDisplayname($_user)
	{
		if(isset($_user['displayname']))
		{
			$displayname = $_user['displayname'];
		}
		else
		{
			$displayname = T_("Unknown");
		}
		return $displayname;
	}


	public static function getUserCode($_user)
	{
		if(isset($_user['id']))
		{
			$user_code = $_user['id'];
			$user_code = \dash\coding::encode($user_code);
		}
		else
		{
			$user_code = null;
		}
		return $user_code;
	}


	public static function dataArray($_args)
	{
		$data = isset($_args['data']) ? $_args['data'] : [];

		if(is_string($data) && (substr($data, 0, 1) === '{' || substr($data, 0, 1) === '['))
		{
			$data = json_decode($data, true);
		}

		return $data;
	}


	public static function addNewTicket($_args, $_user)
	{
		// $tg_msg                      = "🆔#Ticket|code #New \n🗣 ;displayname #user|user_code\n—————\n📬 :ttitle\n:tcontent\n:file\n⏳ |longdatecreated";

		$data = self::dataArray($_args);

		$ttitle   = isset($data['ttitle']) ? $data['ttitle'] : null;
		$tcontent = isset($data['tcontent']) ? $data['tcontent'] : null;
		$file     = isset($data['file']) ? $data['file'] : null;

		$msg                = [];
		$msg['title']       = T_("Add new ticket");
		$msg['content']     = T_(":val add new ticket", ['val' => self::getDisplayname($_user)]);

		$msg['telegram']    = true;
		$msg['need_answer'] = true;

		$tg_msg = '';
		$tg_msg .= "🆔#Ticket".(isset($_args['code']) ? $_args['code']: null);
		$tg_msg .= " #New \n🗣 ". self::getDisplayname($_user). " #user". self::getUserCode($_user);
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
					['/TicketAnswer |code'],
				],
				'one_time_keyboard'  => true,
			],
		];

		return $msg;
	}


	public static function AnswerTicket($_args, $_user)
	{
		// "🆔#Ticket|code 💌:plus \n🗣 ;displayname #user|user_code\n—————\n:tcontent\n:file\n⏳ |longdatecreated"

		$data = self::dataArray($_args);

		$ttitle   = isset($data['ttitle']) ? $data['ttitle'] : null;
		$tcontent = isset($data['tcontent']) ? $data['tcontent'] : null;
		$file     = isset($data['file']) ? $data['file'] : null;
		$plus     = isset($data['plus']) ? $data['plus'] : null;

		$msg                = [];
		$msg['title']       = T_("Answer ticket");
		$msg['content']     = T_(":val answer ticket", ['val' => self::getDisplayname($_user)]);

		$msg['telegram']    = true;
		$msg['need_answer'] = true;

		$tg_msg = '';
		$tg_msg .= "🆔#Ticket".(isset($_args['code']) ? $_args['code']: null);
		$tg_msg .= " 💌". $plus;
		$tg_msg .= "\n🗣 ". self::getDisplayname($_user). " #user". self::getUserCode($_user);
		$tg_msg .= "\n—————\n";

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
					['/TicketAnswer |code'],
				],
				'one_time_keyboard'  => true,
			],
		];

		return $msg;
	}


	public static function AddToTicket($_args, $_user)
	{

      	// "telegram": "🆔#Ticket|code ⚔️:plus \n🗣 ;displayname #user|user_code\n—————\n:tcontent\n:file\n⏳ |longdatecreated"

		$data = self::dataArray($_args);

		$ttitle   = isset($data['ttitle']) ? $data['ttitle'] : null;
		$tcontent = isset($data['tcontent']) ? $data['tcontent'] : null;
		$file     = isset($data['file']) ? $data['file'] : null;
		$plus     = isset($data['plus']) ? $data['plus'] : null;

		$msg                = [];
		$msg['title']       = T_("Add new message to ticket");
		$msg['content']     = T_(":val add new message to ticket", ['val' => self::getDisplayname($_user)]);

		$msg['telegram']    = true;
		$msg['need_answer'] = true;

		$tg_msg = '';
		$tg_msg .= "🆔#Ticket".(isset($_args['code']) ? $_args['code']: null);
		$tg_msg .= " ⚔". $plus;
		$tg_msg .= "\n🗣 ". self::getDisplayname($_user). " #user". self::getUserCode($_user);
		$tg_msg .= "\n—————\n";

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
					['/TicketAnswer |code'],
				],
				'one_time_keyboard'  => true,
			],
		];

		return $msg;
	}


	public static function AddNoteTicket($_args, $_user)
	{

      	// "telegram": "🆔#Ticket|code 🌒️️:plus \n🗣 ;displayname #user|user_code\n—————\n:tcontent\n:file\n⏳ |longdatecreated"

		$data = self::dataArray($_args);

		$ttitle   = isset($data['ttitle']) ? $data['ttitle'] : null;
		$tcontent = isset($data['tcontent']) ? $data['tcontent'] : null;
		$file     = isset($data['file']) ? $data['file'] : null;
		$plus     = isset($data['plus']) ? $data['plus'] : null;

		$msg                = [];
		$msg['title']       = T_("Add new note to ticket");
		$msg['content']     = T_(":val add new note to ticket", ['val' => self::getDisplayname($_user)]);

		$msg['telegram']    = true;
		$msg['need_answer'] = true;

		$tg_msg = '';
		$tg_msg .= "🆔#Ticket".(isset($_args['code']) ? $_args['code']: null);
		$tg_msg .= " 🌒️". $plus;
		$tg_msg .= "\n🗣 ". self::getDisplayname($_user). " #user". self::getUserCode($_user);
		$tg_msg .= "\n—————\n";

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

		return $msg;
	}

}
?>