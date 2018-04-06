<?php
namespace addons\content_api\v1\home;


class model extends \mvc\model
{

	/**
	 * the user id
	 *
	 * @var        integer
	 */
	public $user_id = null;


	/**
	 * the api is telegram bot
	 *
	 * @var        boolean
	 */
	public $telegram_api_mode = false;


	/**
	 * make debug return
	 * default is true
	 * in some where in site this method is false
	 *
	 * @var        boolean
	 */
	public $debug = true;


	/**
	 * the url
	 *
	 * @var        <type>
	 */
	public $url = null;


	/**
	 * the authorization
	 *
	 * @var        <type>
	 */
	public $authorization          = null;


	/**
	 * the parent api key
	 *
	 * @var        <type>
	 */
	public $parent_api_key         = null;
	public $parent_api_key_user_id = 0;


	use tools\_use;


	/**
	 * { function_description }
	 *
	 * @param      <type>  $_name  The name
	 * @param      <type>  $_args  The arguments
	 * @param      <type>  $parm   The parameter
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public function _call($_name, $_args, $parm)
	{
		$this->url = \dash\url::directory();

		\dash\temp::set('api', true);

		$this->api_key();

		if(!\dash\engine\process::status())
		{
			$this->_processor(['force_stop' => true]);
		}

		return parent::_call($_name, $_args, $parm);
	}


	/**
	 * check api key and set the user id
	 */
	public function api_key()
	{

		$api_token = \dash\header::get("api_token") ? \dash\header::get("api_token") : \dash\header::get("Api_token");

		$authorization = \dash\header::get("authorization");
		if($api_token)
		{
			$authorization = $api_token;
		}
		elseif(!$authorization)
		{
			$authorization = \dash\header::get("Authorization");
		}

		if(!$authorization)
		{
			return \dash\notif::error('Authorization not found', 'authorization', 'access');
		}

		// static token list
		$static_token = \dash\option::config('enter', 'static_token');

		if($authorization === \dash\option::config('enter','telegram_hook'))
		{
			$this->telegram_api_mode = true;
			$this->telegram_token();
		}
		elseif(is_array($static_token) && in_array($authorization, $static_token))
		{
			// load user by mobile
			$this->static_token();
		}
		else
		{
			$token = \addons\content_enter\main\tools\token::get_type($authorization);

			if(!\dash\engine\process::status())
			{
				return false;
			}

			switch ($token)
			{

				case 'user_token':
				case 'guest':
					if($this->url == 'v1/token/login' || $this->url == 'v1/token/guest')
					{
						\dash\notif::error(T_("Access denide (Invalid url)"), 'authorization', 'access');
						return false;
					}

					if(!\addons\content_enter\main\tools\token::check($authorization, $token))
					{
						return false;
					}

					$user_id = \addons\content_enter\main\tools\token::get_user_id($authorization);

					if(!$user_id)
					{
						\dash\notif::error(T_("Invalid authorization key (User not found)"), 'authorization', 'access');
						return false;
					}

					$this->user_id = $user_id;
					// init user
					\dash\user::init($user_id);

					break;

				case 'api_key':
					if($this->url != 'v1/token/temp' && $this->url != 'v1/token/guest' && $this->url != 'v1/token/login')
					{
						\dash\notif::error(T_("Access denide to load this url by api key"), 'authorization', 'access');
						return false;
					}
					break;

				default :
					\dash\notif::error(T_("Invalid token"), 'authorization', 'access');
					return false;
			}

			if(isset(\addons\content_enter\main\tools\token::$PARENT['value']))
			{
				$this->parent_api_key = \addons\content_enter\main\tools\token::$PARENT['value'];
			}

			if(isset(\addons\content_enter\main\tools\token::$PARENT['user_id']))
			{
				$this->parent_api_key_user_id = \addons\content_enter\main\tools\token::$PARENT['user_id'];
			}
		}

		$this->authorization = $authorization;
	}


	/**
	 * the api telegram token
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public function static_token()
	{
		$mobile = \dash\header::get("mobile");

		$mobile = \dash\utility\filter::mobile($mobile);
		if(!$mobile)
		{
			\dash\notif::error(T_("Mobile not set"), 'mobile', 'header');
			return false;
		}

		$user_data = \dash\db\users::get_by_mobile($mobile);
		if(isset($user_data['id']))
		{
			$this->user_id = (int) $user_data['id'];
		}
		else
		{
			$signup        = ['mobile' => $mobile];
			$this->user_id = \dash\db\users::signup_quick($signup);
		}
		// init user
		\dash\user::init($this->user_id);

		if(!$this->user_id)
		{
			\dash\db\logs::set('addons:api:static_token:user:not:found:register:faild');
			\dash\notif::error(T_("User not found and can not register the user"), 'static_token', 'header');
			return false;
		}

	}


	/**
	 * the api telegram token
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public function telegram_token()
	{
		$telegramid = \dash\header::get("telegramid");

		if(!$telegramid)
		{
			\dash\notif::error(T_("telegramid is not set"), 'telegramid', 'header');
			return false;
		}

		$where =
		[
			'chatid' => $telegramid,
			'limit'        => 1
		];

		$user_data = \dash\db\config::public_get('users', $where);
		if(!$user_data || !isset($user_data['id']))
		{
			\dash\notif::error(T_("User not found, please register from /enter/hook"), 'telegramid', 'header');
			return false;
		}
		$this->user_id = (int) $user_data['id'];
		// init user
		\dash\user::init($this->user_id);

	}


	/**
	 * save api log
	 *
	 * @param      boolean  $options  The options
	 */
	public function _processor($options = false)
	{
		// $log = [];

		// if(isset($_SERVER['REQUEST_URI']))
		// {
		// 	$log['url'] = $_SERVER['REQUEST_URI'];
		// }

		// if(isset($_SERVER['REQUEST_METHOD']))
		// {
		// 	$log['method'] = $_SERVER['REQUEST_METHOD'];
		// }

		// if(isset($_SERVER['REDIRECT_STATUS']))
		// {
		// 	$log['pagestatus'] = $_SERVER['REDIRECT_STATUS'];
		// }

		// $log['request']        = json_encode(\dash\utility::request(), JSON_UNESCAPED_UNICODE);
		// $log['debug']          = json_encode(\dash\notif::compile(), JSON_UNESCAPED_UNICODE);
		// $log['response']       = json_encode(\dash\notif::get_result(), JSON_UNESCAPED_UNICODE);
		// $log['requestheader']  = json_encode(\dash\header::get('', JSON_UNESCAPED_UNICODE);
		// $log['responseheader'] = json_encode(apache_response_headers(), JSON_UNESCAPED_UNICODE);
		// $log['status']         = \dash\engine\process::status();
		// $log['token']          = $this->authorization;
		// $log['user_id']        = $this->user_id;
		// $log['apikeyuserid']   = $this->parent_api_key_user_id;
		// $log['apikey']         = $this->parent_api_key;
		// $log['clientip']       = \dash\server::ip(true);
		// $log['visit_id']       = null;

		// $log                   = \dash\safe::safe($log);

		// \dash\db\apilogs::insert($log);

		parent::_processor($options);
	}
}
?>