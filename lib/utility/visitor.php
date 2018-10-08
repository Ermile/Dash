<?php
namespace dash\utility;

/** Visitor: handle visitor details **/
class visitor
{

	public static function id()
	{
		$last_id = \dash\session::get('last_id', 'visitor');
		if($last_id)
		{
			return $last_id;
		}
		return null;
	}


	/**
	 * save a visitor in database
	 * @return [type] [description]
	 */
	public static function save()
	{
		if(!defined('db_log_name'))
		{
			return;
		}

		if(\dash\temp::get('force_stop_visitor'))
		{
			return;
		}

		if(\dash\request::is_unload())
		{
			self::save_avg_time();
			return;
		}

		$visitor                  = [];
		$visitor['visitor_ip']    = \dash\server::ip(true);
		$visitor['url_id']        = self::url_id();
		$visitor['url_idreferer'] = self::referer_id($visitor['url_id']);
		$visitor['agent_id']      = self::agent_id();
		$visitor['user_id']       = \dash\user::id();
		$visitor['date']          = date('Y-m-d H:i:s');
		$visitor['session_id']    = session_id();
		$visitor['statuscode']    = http_response_code();
		$visitor['country']       = isset($_SERVER['HTTP_CF_IPCOUNTRY']) ? mb_strtolower($_SERVER['HTTP_CF_IPCOUNTRY']) : null;
		$visitor['method']        = isset($_SERVER['REQUEST_METHOD']) ? mb_strtolower($_SERVER['REQUEST_METHOD']) : null;
		$visitor['avgtime']       = null;

		$result = \dash\db\config::public_insert('visitors', $visitor, \dash\db::get_db_log_name());

		if(\dash\db::get_db_log_name() === true)
		{
			$result = \dash\db::insert_id();
		}
		elseif(isset(\dash\db::$link_open[\dash\db::get_db_log_name()]))
		{
			$result = \dash\db::insert_id(\dash\db::$link_open[\dash\db::get_db_log_name()]);
		}

		if(is_numeric($result))
		{
			self::save_avg_time();
			\dash\session::set('last_id', $result, 'visitor');
			\dash\session::set('last_time', time(), 'visitor');
		}

		return true;
	}


	private static function save_avg_time()
	{
		if(self::id())
		{
			$last_time = \dash\session::get('last_time', 'visitor');
			if($last_time)
			{
				$avgtime = time() - intval($last_time);
				\dash\db\config::public_update('visitors', ['avgtime' => $avgtime], self::id(), \dash\db::get_db_log_name());
			}
		}
	}


	private static function agent_id()
	{
		$agent_session = \dash\session::get('visitor_agent_id');
		if($agent_session && is_numeric($agent_session))
		{
			return intval($agent_session);
		}
		else
		{
			$agent_session = \dash\agent::get(true);
			if($agent_session && is_numeric($agent_session))
			{
				\dash\session::set('agent_id', $agent_session, 'visitor');
				return intval($agent_session);
			}
			else
			{
				return null;
			}
		}
	}


	private static function url_db($_url, $_referer = false)
	{
		$result = \dash\db\config::public_get('urls', ['urlmd5' => md5($_url), 'limit' => 1], ['db_name' => \dash\db::get_db_log_name()]);
		if(isset($result['id']))
		{
			return intval($result['id']);
		}
		else
		{
			$insert_url                = [];
			$insert_url['datecreated'] = date("Y-m-d H:i:s");

			if($_referer)
			{
				$referer                 = urldecode($_url);
				$insert_url['urlmd5']    = md5($referer);
				$insert_url['domain']    = addslashes(parse_url($referer, PHP_URL_SCHEME). '://'. parse_url($referer, PHP_URL_HOST));
				$insert_url['subdomain'] = \dash\url::subdomain();
				$path                    = null;
				$path                    = parse_url($referer, PHP_URL_SCHEME). '://'. $insert_url['domain'];
				$path                    = str_replace($path, '', $referer);
				$path                    = strtok($path);
				$insert_url['path']      = addslashes($path);
				$insert_url['query']     = strpos($referer, '?') ? addslashes(substr($referer, strpos($referer, '?'))) : null;
				$insert_url['pwd']       = addslashes($referer);

			}
			else
			{
				$insert_url['urlmd5']    = md5(\dash\url::pwd());
				$insert_url['domain']    = addslashes(\dash\url::domain());
				$insert_url['subdomain'] = addslashes(\dash\url::subdomain());
				$insert_url['path']      = addslashes(strtok(\dash\url::path(), '?'));
				$insert_url['query']     = addslashes(\dash\url::query());
				$insert_url['pwd']       = addslashes(\dash\url::pwd());
			}

			$result = \dash\db\config::public_insert('urls', $insert_url, \dash\db::get_db_log_name());
			if(\dash\db::get_db_log_name() === true)
			{
				return \dash\db::insert_id();
			}
			elseif(isset(\dash\db::$link_open[\dash\db::get_db_log_name()]))
			{
				return \dash\db::insert_id(\dash\db::$link_open[\dash\db::get_db_log_name()]);
			}
			return null;
		}
	}


	private static function url_id()
	{
		$url = \dash\url::pwd();
		return self::url_db($url, false);
	}


	private static function referer_id($_url_id = null)
	{
		$referer = null;
		if(isset($_SERVER['HTTP_REFERER']))
		{
			$referer = $_SERVER['HTTP_REFERER'];
		}

		if(!$referer)
		{
			return null;
		}

		if($referer === \dash\url::pwd())
		{
			return $_url_id;
		}

		if($referer === \dash\url::site(). '/')
		{
			return null;
		}

		return self::url_db($referer, true);
	}
}
?>