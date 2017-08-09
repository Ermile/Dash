<?php
namespace addons\content_cp\transactions\manage;

use \lib\utility;
use \lib\debug;
class model extends \mvc\model
{
	public function transactions_list($_args, $_fields = [])
	{
		$meta   = [];
		$meta['admin'] = true;

		$search = null;
		if(isset($_args->get("search")[0]))
		{
			$search = $_args->get("search")[0];
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
	 * manage a new record of transaction
	 */
	public function post_manage()
	{

		$caller = utility::post('caller');
		$unit   = utility::post('unit');
		$mobile = utility::post('mobile');
		// $date   = utility::post('date');
		$minus  = utility::post('minus');
		$plus   = utility::post('plus');
		$desc   = utility::post('desc');
		$type   = utility::post('type');

		if(!$caller)
		{
			debug::error(T_("Please select one of the caller items"));
			return false;
		}

		if(!$unit)
		{
			debug::error(T_("Please select one of the unit items"));
			return false;
		}

		if(!$mobile)
		{
			debug::error(T_("Mobile can not be null"));
			return false;
		}

		if(!$type)
		{
			debug::error(T_("Please select one of the type items"));
			return false;
		}

		if(!in_array($type, ['real', 'gift', 'prize', 'transfer']))
		{
			debug::error(T_("Invalid type"));
			return false;
		}

		if(!$plus && !$minus)
		{
			debug::error(T_("Please fill the minus or plus field"));
			return false;
		}

		if($plus && !is_numeric($plus))
		{
			debug::error(T_("Invalid plus field"));
			return false;
		}

		if($minus && !is_numeric($minus))
		{
			debug::error(T_("Invalid minus field"));
			return false;
		}

		$user_id = \lib\db\users::get_by_mobile(\lib\utility\filter::mobile($mobile));
		if(isset($user_id['id']))
		{
			$user_id = $user_id['id'];
		}
		else
		{
			debug::error(T_("Mobile not exist"));
			return false;
		}

		$caller = implode(':', [$caller, $type, $unit]);
		$caller = mb_strtolower($caller);

		if($minus)
		{
			\lib\db\transactions::set($caller, $user_id, ['minus' => $minus, 'desc' => $desc]);
		}
		elseif($plus)
		{
			\lib\db\transactions::set($caller, $user_id, ['plus' => $plus, 'desc' => $desc]);
		}

		if(debug::$status)
		{
			debug::true(T_("Transaction inserted"));
		}
	}
}
?>
