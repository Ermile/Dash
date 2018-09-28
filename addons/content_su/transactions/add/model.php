<?php
namespace content_su\transactions\add;


class model
{

	/**
	 * add a new record of transaction
	 */
	public static function post()
	{
		$id = \dash\request::get('id');

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
			'dateverify' => time(),
		];

		\dash\db\transactions::set($insert);

		if(\dash\engine\process::status())
		{
			\dash\log::set('addTransactionManualy', ['title' => $title, 'plus' => $plus, 'minus' => $minus, 'user_id' => $user_id]);
			\dash\notif::ok(T_("Transaction inserted"));
			\dash\redirect::to(\dash\url::here(). '/transactions');
		}
	}
}
?>
