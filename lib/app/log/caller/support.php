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

		if(isset($_args['code']) && is_numeric($_args['code']))
		{
			$get_ticket = \dash\db\comments::get(['id' => $_args['code'], 'limit' => 1]);

			if(isset($get_ticket['title']))
			{
				$data['title'] = $get_ticket['title'];
			}

			if(isset($get_ticket['content']))
			{
				$data['content'] = $get_ticket['content'];
			}

			if(isset($get_ticket['file']))
			{
				$data['file'] = $get_ticket['file'];
			}
		}

		return $data;
	}


	public static function addNewTicket($_args, $_user)
	{
		// $tg_msg                      = "🆔#Ticket|code #New \n🗣 ;displayname #user|user_code\n—————\n📬 :title\ncontent\nfile\n⏳ |longdatecreated";

		$data = self::dataArray($_args);

		$title   = isset($data['title']) ? $data['title'] : null;
		$content = isset($data['content']) ? $data['content'] : null;
		$file     = isset($data['file']) ? $data['file'] : null;

		$msg                = [];
		$msg['title']       = T_("Add new ticket");
		$msg['content']     = T_(":val add new ticket", ['val' => self::getDisplayname($_user)]);

		$msg['telegram']    = true;
		$msg['need_answer'] = true;

		$code = (isset($_args['code']) ? $_args['code']: null);
		$tg_msg = '';
		$tg_msg .= "🆔#Ticket".$code;
		$tg_msg .= " #New \n🗣 ". self::getDisplayname($_user). " #user". self::getUserCode($_user);
		$tg_msg .= "\n—————\n📬 ";

		if($title)
		{
			$tg_msg .= $title . "\n";
		}

		if($content)
		{
			$content = \dash\app\log\msg::myStripTags($content);
			$tg_msg .= $content . "\n";
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
				'inline_keyboard'    =>
				[
					[
						[
							'text' => 	T_("Visit in site"),
							'url'  => \dash\url::base(). '/!'. $code,
						],
					],
					[
						[
							'text'          => 	T_("Check ticket"),
							'callback_data' => 'ticket '. $code,
						],
					],
					[
						[
							'text'          => 	T_("Answer"),
							'callback_data' => 'ticket '. $code. ' answer',
						],
					],
				],
			],
		];

		return $msg;
	}


	public static function some_in_one($_args, $_user, $_title, $_content, $_gif_url = null, $_tg_msg = null)
	{
		$code = (isset($_args['code']) ? $_args['code']: null);

		$msg                         = [];
		$msg['title']                = $_title;
		$msg['content']              = $_content;

		$msg['telegram']             = true;
		$msg['need_answer']          = true;
		$msg['send_gif']             = $_gif_url ? true : false;
		$msg['not_send_to_userid']   = true;
		$msg['notification']         = true;
		$msg['gif_url']              = $_gif_url;
		$msg['send_msg']             = [];
		$msg['send_msg']['telegram'] = $_tg_msg;
		$msg['send_to']              = ['supervisor'];

		$msg['btn']                  = [];
		$msg['btn']['telegram']      =
		[
			'reply_markup'           =>
			[
				'inline_keyboard'    =>
				[
					[
						[
							'text' => 	T_("Visit in site"),
							'url'  => \dash\url::base(). '/!'. $code,
						],
					],
					[
						[
							'text'          => 	T_("Check ticket"),
							'callback_data' => 'ticket '. $code,
						],
					],
				],
			],
		];

		return $msg;
	}


	public static function seeTicket($_args, $_user)
	{
		$myDisplayName   = self::getDisplayname($_user);
		return self::some_in_one(
			$_args,
			$_user,
			T_("See ticket"),
			T_(":val see ticket", ['val' => $myDisplayName]),
			"https://media.giphy.com/media/3oz8xyBP22S5b6gmsw/giphy.gif",
			"🆔#Ticket|code \n🗣 ;displayname #user|user_code\n—————\nSee the ticket\n⏳ |longdatecreated"
		);
	}


	public static function setUnSolvedTicket($_args, $_user)
	{
		$myDisplayName   = self::getDisplayname($_user);
		return self::some_in_one(
			$_args,
			$_user,
			T_("The ticket set as unsolved ticket"),
			T_(":val set as unsolved the ticket", ['val' => $myDisplayName]),
			null,
			"🆔#Ticket|code ⚠️\n🗣 ;displayname #user|user_code\n—————\nUnsolved ticket\n⏳ |longdatecreated"
		);
	}



	public static function setSolvedTicket($_args, $_user)
	{
		$myDisplayName   = self::getDisplayname($_user);
		return self::some_in_one(
			$_args,
			$_user,
			T_("The ticket set as solved ticket"),
			T_(":val sset as solved the ticket", ['val' => $myDisplayName]),
			"https://media.giphy.com/media/3oz8xZGGYXKrJB5I4g/giphy.gif",
			"🆔#Ticket|code \n🗣 ;displayname #user|user_code\n—————\nSolved ticket\n⏳ {$longdatecreated}"
		);
	}

	public static function setDeleteTicket($_args, $_user)
	{
		$myDisplayName   = self::getDisplayname($_user);
		return self::some_in_one(
			$_args,
			$_user,
			T_("The ticket delete ticket"),
			T_(":val delete the ticket", ['val' => $myDisplayName]),
			null,
			"🆔#Ticket|code 🗑\n🗣 ;displayname #user|user_code\n—————\nDeleted ticket\n⏳ {$longdatecreated}"

		);

	}

	public static function setAwaitingTicket($_args, $_user)
	{
		$myDisplayName   = self::getDisplayname($_user);
		return self::some_in_one(
			$_args,
			$_user,
			T_("The ticket set as open ticket"),
			T_(":val re open the ticket", ['val' => $myDisplayName]),
			null,
			"🆔#Ticket|code 🖐\n🗣 ;displayname #user|user_code\n—————\nAwaiting ticket\n⏳ {$longdatecreated}"
		);
	}

	public static function setCloseTicket($_args, $_user)
	{
		$myDisplayName   = self::getDisplayname($_user);
		return self::some_in_one(
			$_args,
			$_user,
			T_("Close the ticket"),
			T_(":val close the ticket", ['val' => $myDisplayName]),
			null,
			"🆔#Ticket|code 💤\n🗣 ;displayname #user|user_code\n—————\nClose ticket\n⏳ {$longdatecreated}"
		);
	}


	public static function DubleAnswerTicket($_args, $_user)
	{
		$msg                       = self::AnswerTicket($_args, $_user);
		$msg['sms']                = false;
		$msg['not_send_to_userid'] = true;
		return $msg;
	}


	public static function AnswerTicket($_args, $_user)
	{
		// "🆔#Ticket|code 💌:plus \n🗣 ;displayname #user|user_code\n—————\ncontent\nfile\n⏳ |longdatecreated"

		$data = self::dataArray($_args);

		$title   = isset($data['title']) ? $data['title'] : null;
		$content = isset($data['content']) ? $data['content'] : null;
		$file     = isset($data['file']) ? $data['file'] : null;
		$plus     = isset($data['plus']) ? $data['plus'] : null;

		$msg                = [];
		$msg['title']       = T_("Answer ticket");
		$msg['content']     = T_(":val answer ticket", ['val' => self::getDisplayname($_user)]);

		$msg['telegram']    = true;
		$msg['need_answer'] = true;

		$code = (isset($_args['code']) ? $_args['code']: null);
		$tg_msg = '';
		$tg_msg .= "🆔#Ticket".$code;
		$tg_msg .= " 💌". $plus;
		$tg_msg .= "\n🗣 ". self::getDisplayname($_user). " #user". self::getUserCode($_user);
		$tg_msg .= "\n—————\n";

		if($title)
		{
			$tg_msg .= $title . "\n";
		}

		if($content)
		{
			$content = \dash\app\log\msg::myStripTags($content);
			$tg_msg .= $content . "\n";
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
				'inline_keyboard'           =>
				[
					[
						[
							'text' => 	T_("Visit in site"),
							'url' => \dash\url::base(). '/!'. $code,
						],
					],
					[
						[
							'text'          => 	T_("Check ticket"),
							'callback_data' => 'ticket '. $code,
						],
					],
					[
						[
							'text'          => 	T_("Answer"),
							'callback_data' => 'ticket '. $code. ' answer',
						],
					],
				],
			],
		];

		return $msg;
	}


	public static function AddToTicket($_args, $_user)
	{

      	// "telegram": "🆔#Ticket|code ⚔️:plus \n🗣 ;displayname #user|user_code\n—————\ncontent\nfile\n⏳ |longdatecreated"

		$data = self::dataArray($_args);

		$title   = isset($data['title']) ? $data['title'] : null;
		$content = isset($data['content']) ? $data['content'] : null;
		$file     = isset($data['file']) ? $data['file'] : null;
		$plus     = isset($data['plus']) ? $data['plus'] : null;

		$msg                = [];
		$msg['title']       = T_("Add new message to ticket");
		$msg['content']     = T_(":val add new message to ticket", ['val' => self::getDisplayname($_user)]);

		$msg['telegram']    = true;
		$msg['need_answer'] = true;

		$code = (isset($_args['code']) ? $_args['code']: null);
		$tg_msg = '';
		$tg_msg .= "🆔#Ticket".$code;
		$tg_msg .= " ⚔". $plus;
		$tg_msg .= "\n🗣 ". self::getDisplayname($_user). " #user". self::getUserCode($_user);
		$tg_msg .= "\n—————\n";

		if($title)
		{
			$tg_msg .= $title . "\n";
		}

		if($content)
		{
			$content = \dash\app\log\msg::myStripTags($content);
			$tg_msg .= $content . "\n";
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
				'inline_keyboard'           =>
				[
					[
						[
							'text' => 	T_("Visit in site"),
							'url'  => \dash\url::base(). '/!'. $code,
						],
					],
					[
						[
							'text'          => 	T_("Check ticket"),
							'callback_data' => 'ticket '. $code,
						],
					],
					[
						[
							'text'          => 	T_("Answer"),
							'callback_data' => 'ticket '. $code. ' answer',
						],
					],
				],
			],
		];

		return $msg;
	}


	public static function AddNoteTicket($_args, $_user)
	{

      	// "telegram": "🆔#Ticket|code 🌒️️:plus \n🗣 ;displayname #user|user_code\n—————\ncontent\nfile\n⏳ |longdatecreated"

		$data = self::dataArray($_args);

		$title   = isset($data['title']) ? $data['title'] : null;
		$content = isset($data['content']) ? $data['content'] : null;
		$file     = isset($data['file']) ? $data['file'] : null;
		$plus     = isset($data['plus']) ? $data['plus'] : null;

		$msg                = [];
		$msg['title']       = T_("Add new note to ticket");
		$msg['content']     = T_(":val add new note to ticket", ['val' => self::getDisplayname($_user)]);

		$msg['telegram']    = true;
		$msg['need_answer'] = true;

		$code = (isset($_args['code']) ? $_args['code']: null);
		$tg_msg = '';
		$tg_msg .= "🆔#Ticket".$code;
		$tg_msg .= " 🌒️". $plus;
		$tg_msg .= "\n🗣 ". self::getDisplayname($_user). " #user". self::getUserCode($_user);
		$tg_msg .= "\n—————\n";

		if($title)
		{
			$tg_msg .= $title . "\n";
		}

		if($content)
		{
			$content = \dash\app\log\msg::myStripTags($content);
			$tg_msg .= $content . "\n";
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


	public static function answerTicketAlertSend($_args, $_user)
	{
		$code = isset($_args['code']) ? $_args['code']: null;

		$msg             = [];
		$msg['title']    = T_("Regards"). "\n". T_("Ticket :val answered", ['val' => \dash\utility\human::fitNumber($code, false)]);
		$msg['content']  = T_(":val answer your ticket", ['val' => self::getDisplayname($_user)]);

		$msg['telegram'] = true;
		$msg['sms']      = true;

		$code = (isset($_args['code']) ? $_args['code']: null);
		$tg_msg = '';
		$tg_msg .= "🆔#Ticket".$code;
		$tg_msg .= "\n". T_("Regards"). "\n";
		$tg_msg .= "\n". T_("Ticket :val answered", ['val' => $code]). "\n";

		if(isset($_args['datecreated']))
		{
			$tg_msg .= "\n⏳ ". \dash\datetime::fit($_args['datecreated'], true);
		}

		$sms_msg = $msg['title']. "\n" . \dash\url::domain().'/!'. $code;

		$msg['send_msg']             = [];
		$msg['send_msg']['telegram'] = $tg_msg;

		// disable footer in sms
		$msg['send_msg']['footer']   = false;

		$msg['send_msg']['sms']      = $sms_msg;

		return $msg;
	}


	public static function answerTicketAlert($_args, $_user)
	{
		$msg             = self::answerTicketAlertSend($_args, $_user);
		$msg['telegram'] = false;
		$msg['sms']      = false;
		return $msg;
	}


}
?>