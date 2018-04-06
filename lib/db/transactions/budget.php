<?php
namespace dash\db\transactions;


trait budget
{

	public static function calc_budget($_transaction_id, $_plus, $_minus, $_meta = [])
	{
		if(!$_transaction_id || !is_numeric($_transaction_id))
		{
			return false;
		}

		$transaction_detail = self::get(['id' => $_transaction_id, 'limit' => 1]);

		if(!isset($transaction_detail['id']))
		{
			return false;
		}
		$user_as_unverify = false;

		if(array_key_exists('user_id', $transaction_detail))
		{
			if($transaction_detail['user_id'])
			{
				// no problem to continue;
			}
			else
			{
				// user id is null
				// pay as unverify
				$user_as_unverify = true;
			}
		}
		else
		{
			return false;
		}

		if(isset($transaction_detail['type']) && isset($transaction_detail['unit']))
		{
			// no problem to continue;
		}
		else
		{
			return false;
		}

		if($user_as_unverify)
		{
			$budget_before           = self::budget_unverify(['type' => $transaction_detail['type'], 'unit' => $transaction_detail['unit_id']]);
		}
		else
		{
			$budget_before           = self::budget($transaction_detail['user_id'], ['type' => $transaction_detail['type'], 'unit' => $transaction_detail['unit_id']]);
		}

		$budget_before           = floatval($budget_before);

		$budget                  = $budget_before + (floatval($_plus) - floatval($_minus));

		$update                  = $_meta;
		$update['dateverify']    = time();
		$update['budget_before'] = $budget_before;
		$update['budget']        = $budget;

		\lib\db\transactions::update($update, $_transaction_id);

	}
	/**
	 * get the budget of users
	 *
	 * @param      <type>  $_user_id  The user identifier
	 */
	public static function budget_unverify($_options = [])
	{
		$default_options =
		[
			'type' => null,
			'unit' => null,
		];

		if(!is_array($_options))
		{
			$_options = [];
		}
		$_options = array_merge($default_options, $_options);

		$unit = null;
		if(isset($_options['unit']) && is_numeric($_options['unit']))
		{
			$unit = " AND transactions.unit_id = $_options[unit] ";
		}

		$only_one_value = false;
		$field = ['type','budget'];

		if($_options['type'])
		{
			$only_one_value = true;
			$field          = 'budget';
			$query =
			"
				SELECT budget
				FROM transactions
				WHERE
					transactions.type    = '$_options[type]' AND
					transactions.verify  = 1
					$unit
				ORDER BY transactions.dateverify DESC
				LIMIT 1
			";
		}
		else
		{
			return false;
		}

		$result = \lib\db::get($query, $field, $only_one_value);

		if(!$result)
		{
			return 0;
		}

		return $result;
	}


	/**
	 * get the budget of users
	 *
	 * @param      <type>  $_user_id  The user identifier
	 */
	public static function budget($_user_id, $_options = [])
	{
		$default_options =
		[
			'type' => null,
			'unit' => null,
		];

		if(!is_array($_options))
		{
			$_options = [];
		}
		$_options = array_merge($default_options, $_options);

		if($_options['unit'] === 'all')
		{
			$all_unit =
			"
			SELECT
				transactions.budget AS `budget`,
				transactions.unit_id AS `unit`
			FROM
				transactions
			WHERE
				transactions.id IN
				(
					SELECT
						MAX(transactions.dateverify)
					FROM
						transactions
					WHERE
						transactions.user_id = $_user_id AND
						transactions.verify  = 1
					GROUP BY
						transactions.unit_id
				)
			-- get all budget in all units of users
			";
			$all_unit =  \lib\db::get($all_unit, ['unit', 'budget']);
			return $all_unit;
		}

		$unit = null;
		if(isset($_options['unit']) && is_numeric($_options['unit']))
		{
			$unit = " AND transactions.unit_id = $_options[unit] ";
		}

		$only_one_value = false;
		$field = ['type','budget'];

		if($_options['type'])
		{
			$only_one_value = true;
			$field          = 'budget';
			$query =
			"
				SELECT budget
				FROM transactions
				WHERE
					transactions.user_id = $_user_id AND
					transactions.type    = '$_options[type]' AND
					transactions.verify  = 1
					$unit
				ORDER BY transactions.dateverify DESC
				LIMIT 1
			";
		}
		else
		{

			$query =
			"("."

				SELECT budget, 'gift' AS `type`
				FROM transactions
				WHERE
					transactions.user_id = $_user_id AND
					transactions.type    = 'gift' AND
					transactions.verify  = 1
					$unit
				ORDER BY transactions.dateverify DESC
				LIMIT 1
			)
			UNION ALL
			(
				SELECT budget, 'promo' AS `type`
				FROM transactions
				WHERE
					transactions.user_id = $_user_id AND
					transactions.type    = 'promo' AND
					transactions.verify  = 1
					$unit
				ORDER BY transactions.dateverify DESC
				LIMIT 1
			)
			UNION ALL
			(
				SELECT budget, 'prize' AS `type`
				FROM transactions
				WHERE
					transactions.user_id = $_user_id AND
					transactions.type    = 'prize' AND
					transactions.verify  = 1
					$unit
				ORDER BY transactions.dateverify DESC
				LIMIT 1
			)
			UNION ALL
			(
				SELECT budget, 'transfer' AS `type`
				FROM transactions
				WHERE
					transactions.user_id = $_user_id AND
					transactions.type    = 'transfer' AND
					transactions.verify  = 1
					$unit
				ORDER BY transactions.dateverify DESC
				LIMIT 1
			)
			UNION ALL
			(
				SELECT budget, 'money' AS `type`
				FROM transactions
				WHERE
					transactions.user_id = $_user_id AND
					transactions.type    = 'money' AND
					transactions.verify  = 1
					$unit
				ORDER BY transactions.dateverify DESC
				LIMIT 1
			)
			";

		}
		$result = \lib\db::get($query, $field, $only_one_value);
		if(!$result)
		{
			return 0;
		}
		return $result;
	}
}
?>
