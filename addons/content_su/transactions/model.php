<?php
namespace addons\content_su\transactions;


class model extends \addons\content_su\main\model
{
	public function transactions_list($_args, $_fields = [])
	{
		$meta   = [];
		$meta['admin'] = true;

		$search = null;
		if(\lib\request::get('search'))
		{
			$search = \lib\request::get('search');
		}

		foreach ($_fields as $key => $value)
		{
			if(isset($_args->get($value)[0]))
			{
				$meta[$value] = $_args->get($value)[0];
			}
		}

		$result = \lib\db\transactions::search($search, $meta);
		return $result;
	}

	/**
	 * Loads my transaction.
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     array   ( description_of_the_return_value )
	 */
	public function loadMyTransaction($_args)
	{
		$id     = isset($_args->match->url[0][1]) ? $_args->match->url[0][1] : null;
		$result = [];

		if($id)
		{
			$result = \lib\db\transactions::get(['id' => $id, 'limit' => 1]);
		}
		return $result;
	}


	/**
	 * add a new record of transaction
	 */
	public function post_add($_args)
	{
		$id = isset($_args->match->url[0][1]) ? $_args->match->url[0][1] : null;


		if(!is_numeric($id))
		{
			$id = null;
		}

		$log_meta =
		[
			'data' => $id,
			'meta' =>
			[
				'input'   => \lib\request::post(),
				'session' => $_SESSION
			],
		];


		$title  = \lib\request::post('title');
		$unit   = \lib\request::post('unit');
		$mobile = \lib\request::post('mobile');
		$minus  = \lib\request::post('minus');
		$plus   = \lib\request::post('plus');
		$desc   = \lib\request::post('desc');
		$type   = \lib\request::post('type');

		if(!$this->login())
		{
			\lib\debug::error(T_("You must login to add new transaction"));
			return false;
		}

		$user_id = $this->login('id');

		if(!$title)
		{
			\lib\db\logs::set('su:transactions:add:title:not:set', $user_id, $log_meta);
			\lib\debug::error(T_("Please set the transaction title"));
			return false;
		}

		if(!$unit)
		{
			\lib\db\logs::set('su:transactions:add:unit:not:set', $user_id, $log_meta);
			\lib\debug::error(T_("Please select one of the unit items"));
			return false;
		}


		if(!$mobile)
		{
			\lib\db\logs::set('su:transactions:add:mobile:not:set', $user_id, $log_meta);
			\lib\debug::error(T_("Mobile can not be null"));
			return false;
		}

		if(!$type)
		{
			\lib\db\logs::set('su:transactions:add:type:not:set', $user_id, $log_meta);
			\lib\debug::error(T_("Please select one of the type items"));
			return false;
		}


		if(!in_array($type, ['money', 'gift', 'prize', 'transfer']))
		{
			\lib\db\logs::set('su:transactions:add:invalid:type', $user_id, $log_meta);
			\lib\debug::error(T_("Invalid type"));
			return false;
		}

		if(!$plus && !$minus)
		{
			\lib\db\logs::set('su:transactions:add:no:minus:no:plus', $user_id, $log_meta);
			\lib\debug::error(T_("Please fill the minus or plus field"));
			return false;
		}

		if($plus && !is_numeric($plus))
		{
			\lib\db\logs::set('su:transactions:add:plus:isnot:numeric', $user_id, $log_meta);
			\lib\debug::error(T_("Invalid plus field"));
			return false;
		}


		if($minus && !is_numeric($minus))
		{
			\lib\db\logs::set('su:transactions:add:minus:isnot:numeric', $user_id, $log_meta);
			\lib\debug::error(T_("Invalid minus field"));
			return false;
		}

		$user_id = \lib\db\users::get_by_mobile(\lib\utility\filter::mobile($mobile));
		if(isset($user_id['id']))
		{
			$user_id = $user_id['id'];
		}
		else
		{
			\lib\db\logs::set('su:transactions:add:mobile:notexist', $user_id, $log_meta);
			\lib\debug::error(T_("Mobile not exist"));
			return false;
		}

		if($plus && $minus)
		{
			\lib\db\logs::set('su:transactions:add:plus:and:minus:set', $user_id, $log_meta);
			\lib\debug::error(T_("One of the plus or minus field must be set"));
			return false;
		}

		if($minus)
		{
			$plus = null;
		}
		else
		{
			$minus = null;
		}

		$insert =
		[
			'caller'    => 'manually',
			'title'     => $title,
			'user_id'   => $user_id,
			'plus'      => $plus,
			'minus'     => $minus,
			'payment'   => null,
			'type'      => $type,
			'unit'      => $unit,
			'date'      => date("Y-m-d H:i:s"),
			'parent_id' => $id,
			'verify'    => 1,
		];

		\lib\db\transactions::set($insert);

		if(\lib\debug::$status)
		{
			\lib\debug::true(T_("Transaction inserted"));
			$this->redirector(\lib\url::here(). '/transactions');
		}
	}
}
?>
