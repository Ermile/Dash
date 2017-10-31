<?php
namespace content_account\billing;
use \lib\utility;
use \lib\debug;
use \lib\utility\payment;

class model extends \mvc\model
{

	/**
	 * the user id
	 *
	 * @var        <type>
	 */
	public $user_id = null;


	/**
	 * get billing data to show
	 */
	public function get_billing($_args)
	{
		if(!$this->login())
		{
			return false;
		}
		$meta            = [];
		$this->user_id   = $this->login('id');
		$meta['user_id'] = $this->user_id;
		// $meta['admin']   = false ;
		$meta['verify'] = 1;
		$billing_history = \lib\db\transactions::search(null, $meta);
		return $billing_history;
	}

	/**
	 * post data and update or insert billing data
	 */
	public function post_billing()
	{
		if(!$this->login())
		{
			debug::error(T_("You must login to pay amount"));
			return false;
		}

		if(utility::post('type') === 'promo')
		{
			if(utility::post('promo'))
			{
				$this->check_promo();
				return;
			}
			else
			{
				debug::error(T_("Invalid promo code"), 'promo', 'arguments');
				return false;
			}
		}

		$meta = ['turn_back' => $this->url('full')];

		\lib\utility\payment\pay::start($this->login('id'), utility::post('bank'), utility::post('amount'), $meta);
	}



	public function check_promo()
	{
		$promo     = utility::post('promo');
		$amount    = 0;
		$shcode = null;
		$ref  = null;

		$log_meta =
        [
        	'data' => null,
        	'meta' =>
        	[
				'user'    => $this->login('id'),
				'ref'     => $ref,
				'post'    => utility::post(),
				'session' => $_SESSION,
        	],
        ];

		if(!preg_match("/^ref\_([A-Za-z0-9]+)$/", $promo, $split))
		{
			\lib\db\logs::set('ref:reqular:invalid', $this->login('id'), $log_meta);
			debug::error(T_("Invalid promo code"), 'promo', 'arguments');
			return false;
		}
		if(isset($split[1]))
		{
			$shcode = $split[1];
			$ref = \lib\utility\shortURL::decode($shcode);
			if(!$ref)
			{
				\lib\db\logs::set('ref:shortURL:invalid', $this->login('id'), $log_meta);
				debug::error(T_("Invalid promo code"), 'promo', 'arguments');
				return false;
			}
		}

		if(intval($this->login('id')) === intval($ref))
		{
			\lib\db\logs::set('ref:yourself', $this->login('id'), $log_meta);
			debug::error(T_("You try to referral yourself!"), 'promo', 'arguments');
			return false;
		}

		if($this->login('ref'))
		{
			\lib\db\logs::set('ref:full', $this->login('id'), $log_meta);
			debug::error(T_("You have ref. can not set another ref"), 'promo', 'arguments');
			return false;
		}

		$check_ref = \lib\db\users::get_by_id($ref);

		if(!isset($check_ref['id']))
		{
			\lib\db\logs::set('ref:user:not:found', $this->login('id'), $log_meta);
			debug::error(T_("Ref not found"), 'promo', 'arguments');
			return false;
		}

		\lib\db\users::update(['ref' => $ref], $this->login('id'));
		$_SESSION['user']['ref'] = $ref;

		$transaction_set =
        [
			'caller'          => 'promo:ref',
			'title'           => T_("Promo for using ref"),
			'user_id'         => $this->login('id'),
			'plus'            => 10 * 1000,
			'payment'         => null,
			'related_foreign' => 'users',
			'related_id'      => $ref,
			'verify'          => 1,
			'type'            => 'money',
			'unit'            => 'toman',
			'date'            => date("Y-m-d H:i:s"),
        ];

        \lib\db\transactions::set($transaction_set);


        $notify_ref =
        [
			'to'      => $ref,
			'cat'     => 'ref',
			'content' => T_("Someone used your ref link in her referral"),
        ];
        \lib\db\notifications::set($notify_ref);


        $notify_ref =
        [
			'to'      => $this->login('id'),
			'cat'     => 'useref',
			'content' => T_("Your are using referral program and your account was charged"),
        ];
        \lib\db\notifications::set($notify_ref);


        if(debug::$status)
        {
        	\lib\db\logs::set('user:use:ref', $this->login('id'), $log_meta);
        	\lib\db\logs::set('user:was:ref', $ref, $log_meta);
        	debug::true(T_("Your ref was set and your account was charge"));
        }
	}



}
?>