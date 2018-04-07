<?php
namespace content_su\transactions;


class model extends \addons\content_su\main\model
{
	public function transactions_list($_args, $_fields = [])
	{
		$meta   = [];
		$meta['admin'] = true;

		$search = null;
		if(\dash\request::get('search'))
		{
			$search = \dash\request::get('search');
		}

		foreach ($_fields as $key => $value)
		{
			if(isset($_args->get($value)[0]))
			{
				$meta[$value] = $_args->get($value)[0];
			}
		}

		$result = \dash\db\transactions::search($search, $meta);
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
			$result = \dash\db\transactions::get(['id' => $id, 'limit' => 1]);
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
				'input'   => \dash\request::post(),
				'session' => $_SESSION
			],
		];


		$title  = \dash\request::post('title');
		$unit   = \dash\request::post('unit');
		$mobile = \dash\request::post('mobile');
		$minus  = \dash\request::post('minus');
		$plus   = \dash\request::post('plus');
		$desc   = \dash\request::post('desc');
		$type   = \dash\request::post('type');

		if(!\dash\user::login())
		{
			\dash\notif::error(T_("You must login to add new transaction"));
			return false;
		}

		$user_id = \dash\user::id();

		if(!$title)
		{
			\dash\db\logs::set('su:transactions:add:title:not:set', $user_id, $log_meta);
			\dash\notif::error(T_("Please set the transaction title"));
			return false;
		}

		if(!$unit)
		{
			\dash\db\logs::set('su:transactions:add:unit:not:set', $user_id, $log_meta);
			\dash\notif::error(T_("Please select one of the unit items"));
			return false;
		}


		if(!$mobile)
		{
			\dash\db\logs::set('su:transactions:add:mobile:not:set', $user_id, $log_meta);
			\dash\notif::error(T_("Mobile can not be null"));
			return false;
		}

		if(!$type)
		{
			\dash\db\logs::set('su:transactions:add:type:not:set', $user_id, $log_meta);
			\dash\notif::error(T_("Please select one of the type items"));
			return false;
		}


		if(!in_array($type, ['money', 'gift', 'prize', 'transfer']))
		{
			\dash\db\logs::set('su:transactions:add:invalid:type', $user_id, $log_meta);
			\dash\notif::error(T_("Invalid type"));
			return false;
		}

		if(!$plus && !$minus)
		{
			\dash\db\logs::set('su:transactions:add:no:minus:no:plus', $user_id, $log_meta);
			\dash\notif::error(T_("Please fill the minus or plus field"));
			return false;
		}

		if($plus && !is_numeric($plus))
		{
			\dash\db\logs::set('su:transactions:add:plus:isnot:numeric', $user_id, $log_meta);
			\dash\notif::error(T_("Invalid plus field"));
			return false;
		}


		if($minus && !is_numeric($minus))
		{
			\dash\db\logs::set('su:transactions:add:minus:isnot:numeric', $user_id, $log_meta);
			\dash\notif::error(T_("Invalid minus field"));
			return false;
		}

		$user_id = \dash\db\users::get_by_mobile(\dash\utility\filter::mobile($mobile));
		if(isset($user_id['id']))
		{
			$user_id = $user_id['id'];
		}
		else
		{
			\dash\db\logs::set('su:transactions:add:mobile:notexist', $user_id, $log_meta);
			\dash\notif::error(T_("Mobile not exist"));
			return false;
		}

		if($plus && $minus)
		{
			\dash\db\logs::set('su:transactions:add:plus:and:minus:set', $user_id, $log_meta);
			\dash\notif::error(T_("One of the plus or minus field must be set"));
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

		\dash\db\transactions::set($insert);

		if(\dash\engine\process::status())
		{
			\dash\notif::ok(T_("Transaction inserted"));
			\dash\redirect::to(\dash\url::here(). '/transactions');
		}
	}
}
?>
