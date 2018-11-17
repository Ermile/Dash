<?php
namespace dash\app\log\caller;

class addNewTicket extends \dash\app\log\caller
{


	public static function list($_args = [])
	{
		$arg                = [];
		$arg['title']       = T_("Add new ticket");
		$arg['content']     = parent::displayname($_args) . ' '. T_("add new ticket");
		return $arg;
	}


	public static function is_notif()
	{
		return true;
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
}
?>