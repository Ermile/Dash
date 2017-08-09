<?php
namespace addons\content_cp\options;

use \lib\utility;
use \lib\debug;

class model extends \addons\content_cp\home\model
{
	/**
	 * Update options data
	 * @return run update query and no return value
	 */
	function put_options()
	{
		$newOptions = null;
		if(\lib\utility::post('reset') === 'reset' || \lib\utility::get('action') === 'reset')
		{
			$newOptions = $this->getDefault();
			\lib\debug::msg('direct', true);
		}
		else
		{
			$newOptions = $this->getOptions();
		}
		foreach ($newOptions as $group => $record)
		{
			foreach ($record as $field => $value)
			{
				$meta   = null;
				$status = 'enable';
				$qry    = $this->sql()->table('options')
					->where('option_cat', 'option_'.$group)
					->and('option_key',   $field)
					->and('post_id',      '#NULL')
					->and('user_id',      '#NULL');
				$fieldExist = $qry->select()->num();
				// if exist more than 2 times remove all the properties
				if($fieldExist > 1)
				{
					debug::true(T_("We find a problem and solve it!"));
					$qry->delete();
					$fieldExist = 0;
				}

				// for array seperate it intro value and meta and encode it
				if(is_array($value))
				{
					// set meta values
					if(isset($value['meta']))
					{
						// do something in config
						if($field === 'config')
						{
							$this->doConfig($value['meta']);
						}
						elseif($field === 'telegram')
						{
							$this->doTelegram($value['meta']);
						}
						$meta   = json_encode($value['meta'], JSON_FORCE_OBJECT | JSON_HEX_QUOT | JSON_HEX_APOS | JSON_UNESCAPED_UNICODE);
						// $meta   = $value['meta'];
					}
					// set status if exist
					if(array_key_exists('status', $value))
					{
						$status = $value['status']? 'enable': 'disable';
					}
					// set value
					if(array_key_exists('value', $value))
					{
						$value  = $value['value'];
					}
					else
					{
						$value = null;
					}
				}
				// if value is empty set it empty
				if(!$value)
					$value = '#""';

				$qry = $qry
					->set('option_cat',    'option_'.$group)
					->set('option_status', $status)
					->set('option_key',    $field)
					->set('option_value',  $value);

				// if meta is not empty then add it to insert query
				if(isset($meta) && $meta !== '""')
				{
					$qry = $qry->set('option_meta', $meta);
				}

				// if exist update field
				if($fieldExist == 1)
				{
					// var_dump($qry->updateString());
					$qry->update();
				}
				// else if not exist insert this field to table
				else
				{
					$qry->insert('IGNORE');
				}
			}
		}


		$this->commit(function()
		{
			debug::true(T_("Update Successfully"));
			// $this->redirector()->set_url($_module.'/edit='.$_postId);
		});

		// if a query has error or any error occour in any part of codes, run roolback
		$this->rollback(function()
		{
			debug::title(T_("Transaction error").': ');
		} );
	}


	/**
	 * get post variables and fill it in array
	 * @return [array] contain list of all data entered
	 */
	private function getOptions()
	{
		$mainsite_url = Domain;
		$redirectURL  = Domain.'.'.Tld;
		// save domain name
		if(utility::post('config-domainSame') && strlen(utility::post('config-domainName')) > 1)
		{
			$mainsite_url = utility::post('config-domainName');
		}
		// save domain tld
		if(utility::post('config-defaultTld'))
		{
			$mainsite_url .= '.' .utility::post('config-defaultTld');
		}
		else
		{
			$mainsite_url .= MainTld;
		}

		// calculate hook url
		$hook_url = null;
		if(strlen(utility::post('tg-hookFolder')) > 3)
		{
			$hook_url = 'https://'. $mainsite_url. '/saloos_tg/';
			$botName = utility::post('tg-bot');
			if($botName && strlen($botName) > 3)
			{
				$hook_url .= $botName.'/';
			}
			$hook_url .= utility::post('tg-hookFolder');
		}
		$protocol = 'http://';
		if(utility::post('config-https'))
		{
			$protocol = 'https://';
		}
		if(utility::post('config-redirectToMain'))
		{
			$redirectURL = $mainsite_url;
		}
		// complete url by add protocol
		$mainsite_url = $protocol. $mainsite_url;
		$redirectURL  = $protocol. $redirectURL;

		$myOptions =
		[
			'general' =>
			[
				'title'    => utility::post('site-title'),
				'desc'     => utility::post('site-desc'),
				'email'    => utility::post('site-email'),
				'url'      => utility::post('site-url'),
				'tabColor' => utility::post('site-tabColor'),
			],
			'config' =>
			[
				'config' =>
				[
					'meta'   =>
					[
						'coming'         => utility::post('config-coming'),
						'debug'          => utility::post('config-debug'),
						'saveAsCookie'   => utility::post('config-cookie'),
						'logVisitors'    => utility::post('config-logVisitors'),
						'useMainAccount' => utility::post('config-useMainAccount'),
						'mainAccount'    => utility::post('config-mainAccount'),
						'defaultLang'    => utility::post('config-defaultLang'),
						'fakeSub'        => utility::post('config-fakeSub'),
						'https'          => utility::post('config-https'),
						'shortURL'       => utility::post('config-shortURL'),
						'forceShortURL'  => utility::post('config-forceShortURL'),
						'sms'            => utility::post('config-sms'),
						'social'         => utility::post('config-social'),
						'account'        => utility::post('config-account'),
						'multiDomain'    => utility::post('config-multiDomain'),
						'defaultTld'     => utility::post('config-defaultTld'),
						'domainSame'     => utility::post('config-domainSame'),
						'domainName'     => utility::post('config-domainName'),
						'redirectToMain' => utility::post('config-redirectToMain'),
						'redirectURL'    => $redirectURL,
						'mainSite'       => $mainsite_url,
					],
				],
			],
			'social' =>
			[
				'social' =>
				[
					'status' => utility::post('config-social'),
					'value'  => null,
					'meta'   =>
					[
						'twitter'    => utility::post('twitter'),
						'facebook'   => utility::post('facebook'),
						'googleplus' => utility::post('googleplus'),
						'github'     => utility::post('github'),
						'linkedin'   => utility::post('linkedin'),
						'telegram'   => utility::post('telegram'),
						'aparat'     => utility::post('aparat'),
					]

				],
				'twitter' =>
				[
					'status' => utility::post('twitter-status'),
					'value'  => utility::post('twitter'),
					'meta'   =>
					[
						'ConsumerKey'       => utility::post('twitter-ConsumerKey'),
						'ConsumerSecret'    => utility::post('twitter-ConsumerSecret'),
						'AccessToken'       => utility::post('twitter-AccessToken'),
						'AccessTokenSecret' => utility::post('twitter-AccessTokenSecret')
					]
				],
				'facebook' =>
				[
					'status' => utility::post('fb-status'),
					'value'  => utility::post('facebook'),
					'meta'   =>
					[
						'app_id'         => utility::post('fb-app_id'),
						'app_secret'     => utility::post('fb-app_secret'),
						'redirect_url'   => utility::post('fb-redirect_url'),
						'required_scope' => utility::post('fb-required_scope'),
						'page_id'        => utility::post('fb-page_id'),
						'access_token'   => utility::post('fb-access_token'),
						'client_token'   => utility::post('fb-client_token')
					]
				],
				'googleplus' =>
				[
					'value' => utility::post('googleplus'),
					'meta'  => ''
				],
				'github' =>
				[
					'value' => utility::post('github'),
					'meta'  => ''
				],
				'linkedin' =>
				[
					'value' => utility::post('linkedin'),
					'meta'  => ''
				],
				'telegram' =>
				[
					'status' => utility::post('tg-status'),
					'value'  => utility::post('telegram'),
					'meta'   =>
					[
						'key'        => utility::post('tg-key'),
						'bot'        => utility::post('tg-bot'),
						'hookFolder' => utility::post('tg-hookFolder'),
						'hook'       => $hook_url,
						'debug'      => utility::post('tg-debug'),
						'channel'    => utility::post('tg-channel'),
						'botan'      => utility::post('tg-botan'),
					]
				],
				'aparat' =>
				[
					'value' => utility::post('aparat'),
					'meta'  => ''
				],
			],
			'sms' =>
			[
				'sms' =>
				[
					'status' => utility::post('config-sms'),
					'value'  => utility::post('sms-name'),
					'meta'   =>
					[
						'apikey'       => utility::post('sms-apikey'),
						'debug'        => utility::post('sms-debug'),
						'line1'        => utility::post('sms-line1'),
						'line2'        => utility::post('sms-line2'),
						'iran'         => utility::post('sms-iran'),
						'header'       => utility::post('sms-header'),
						'footer'       => utility::post('sms-footer'),
						'one'          => utility::post('sms-one'),
						'signup'       => utility::post('sms-signup'),
						'verification' => utility::post('sms-verification'),
						'recovery'     => utility::post('sms-recovery'),
						'changepass'   => utility::post('sms-changepass'),
					]
				],
			],
			'account' =>
			[
				'account' =>
				[
					'status' => utility::post('config-account'),
					'value'  => utility::post('account-default'),
					'meta'   =>
					[
						'passphrase' => utility::post('account-passphrase'),
						'passkey'    => utility::post('account-passkey'),
						'passvalue'  => utility::post('account-passvalue'),
						'default'    => utility::post('account-default'),
						'redirect'   => utility::post('account-redirect'),
						'register'   => utility::post('account-register'),
						'recovery'   => utility::post('account-recovery'),
					]
				]
			],
		];

		return $myOptions;
	}


	/**
	 * get post variables and fill it in array for default condition
	 * @return [array] contain list of all data entered
	 */
	private function getDefault()
	{
		$myDefaults =
		[
			'general' =>
			[
				'title' => 'Ermile',
				'desc'  => 'Powered by Saloss',
			],
			'config' =>
			[
				'config' =>
				[
					'meta'   =>
					[
						'logVisitors'    => 'on',
						'defaultLang'    => 'en_US',
						'fakeSub'        => 'on',
						'account'        => 'on',
					],
				],
			],
			'sms' =>
			[
				'sms' =>
				[
					'meta'   =>
					[
						'one'          => 'on',
						'signup'       => 'on',
						'verification' => 'on',
						'recovery'     => 'on',
						'changepass'   => 'on',
					]
				],
			],
			'account' =>
			[
				'account' =>
				[
					'status' => 'on',
					'value'  => utility::post('account-default'),
					'meta'   =>
					[
						'redirect'   => 'cp',
					]
				]
			],
		];

		return $myDefaults;
	}


	/**
	 * draw options fields
	 * @param  [type] $_key [description]
	 * @return [type]       [description]
	 */
	public function draw_options($_key = null)
	{
		// get options
		$qry = $this->sql()->table('options')
			->where('option_cat', 'like', "'option%'")
			->and('post_id',      '#NULL')
			->and('user_id',      '#NULL');

		// change options to special array to fill it
		$result    = [];
		$datatable = $qry->select()->allassoc();
		foreach ($datatable as $datarow)
		{
			$cat    = $datarow['option_cat'];
			$cat    = substr($cat, 7);
			$key    = $datarow['option_key'];
			$status = $datarow['option_status'];
			$value  = $datarow['option_value'];
			$meta   = $datarow['option_meta'];
			$meta   = json_decode($meta, true);

			if(isset($meta))
			{
				$result[$cat][$key]['status'] = $status;
				$result[$cat][$key]['value']  = $value;
				$result[$cat][$key]['meta']   = $meta;
			}
			elseif(isset($value) && $value === '-status-')
			{
				$result[$cat][$key] = $status;
			}
			else
			{
				// $result[$cat][$key] = $value;
				$result[$cat][$key]['status'] = $status;
				$result[$cat][$key]['value']  = $value;
				$result[$cat][$key]['meta']   = $meta;
			}
		}

		return $result;
	}


	/**
	 * run something for config of options
	 * @return [type] [description]
	 */
	public function doConfig($_config)
	{
		// run visitor table installation
		// need to run only one times
		if(isset($_config['logVisitors']))
		{
			$result = \lib\utility\visitor::install();
			if (!in_array(false, $result))
			{
				debug::true(T_("Start logging visitors"));
			}
		}
		if(isset($_config['coming']))
		{
			setcookie('preview','yes',time() + 365*24*60*60,'/','.'.Service);
		}
	}


	/**
	 * do telegram settings
	 * @param  [type] $_options [description]
	 * @return [type]           [description]
	 */
	public function doTelegram($_options)
	{
		// if key is fake do not run telegram hook
		if(!isset($_options['key']) || strlen($_options['key']) < 20)
		{
			return null;
		}
		if(isset($_options['debug']) && $_options['debug'])
		{
			return false;
		}
		if(isset($_options['hook']))
		{
			$result = \lib\telegram\tg::setWebhook();
		}
		else
		{
			$result = \lib\telegram\tg::unsetWebhook();
		}
		debug::true($result);
	}
}
?>