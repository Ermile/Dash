<?php
namespace dash;

class agent
{
	public static function get($_id = false)
	{
		$agent = self::agent(true);
		if($_id)
		{
			$check_exits_agent = \dash\db\agents::get(['agentmd5' => md5($agent), 'limit' => 1]);

			if(isset($check_exits_agent['id']))
			{
				return intval($check_exits_agent['id']);
			}

			$is_bot       = self::isBot();

			$agent_detail = \dash\utility\browserDetection::browser_detection('full_assoc');

			// can not detect agent
			if(!$agent)
			{
				return null;
			}

			$insert =
			[
				'agent'    => $agent,
				'agentmd5' => md5($agent),
				'group'    => isset($agent_detail['browser_working']) ? $agent_detail['browser_working'] : null,
				'name'     => isset($agent_detail['browser_name']) 	 ? $agent_detail['browser_name'] 	: null,
				'version'  => isset($agent_detail['browser_number'])  ? $agent_detail['browser_number'] 	: null,
				'os'       => isset($agent_detail['os']) 			 ? $agent_detail['os'] 				: null,
				'osnum'    => isset($agent_detail['os_number']) 		 ? $agent_detail['os_number'] 		: null,
				'meta'     => json_encode($agent_detail, true),
				'robot'    => $is_bot ? 1 : null,
			];

			$agent_id = \dash\db\agents::insert($insert);

			return $agent_id;
		}
		else
		{
			return $agent;
		}

	}


	/**
	 * return agent of visitor in current page
	 * @return [type] [description]
	 */
	public static function agent($_encode = true)
	{
		$agent = null;
		if(isset($_SERVER['HTTP_USER_AGENT']))
		{
			$agent = $_SERVER['HTTP_USER_AGENT'];
		}
		// if user want encode referer
		if($_encode)
		{
			$agent = urlencode($agent);
		}
		return $agent;
	}



	/**
	 * check current user is bot or not
	 * @return boolean [description]
	 */
	public static function isBot()
	{
		$robot   = null;
		$agent   = self::agent();
		$botlist =
		[
			"Teoma",
			"alexa",
			"froogle",
			"Gigabot",
			"inktomi",
			"looksmart",
			"URL_Spider_SQL",
			"Firefly",
			"NationalDirectory",
			"Ask Jeeves",
			"TECNOSEEK",
			"InfoSeek",
			"WebFindBot",
			"girafabot",
			"crawler",
			"www.galaxy.com",
			"Googlebot",
			"Scooter",
			"Slurp",
			"msnbot",
			"appie",
			"FAST",
			"WebBug",
			"Spade",
			"ZyBorg",
			"rabaz",
			"Baiduspider",
			"Feedfetcher-Google",
			"TechnoratiSnoop",
			"Rankivabot",
			"Mediapartners-Google",
			"Sogou web spider",
			"WebAlta Crawler",
			"TweetmemeBot",
			"Butterfly",
			"Twitturls",
			"Me.dium",
			"Twiceler",
			"inoreader",
			"yoozBot",
		];

		foreach($botlist as $bot)
		{
			if(strpos($agent, $bot) !== false)
			{
				$robot = true;
			}
		}
		// return result
		return $robot;
	}


}
?>