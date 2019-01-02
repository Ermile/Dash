<?php
namespace dash\db\transactions;


trait budget
{

	public static function calc_budget($_detail)
	{
		if(!isset($_detail['id']))
		{
			return false;
		}

		$user_as_unverify = false;

		if(array_key_exists('user_id', $_detail))
		{
			if($_detail['user_id'])
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

		if(isset($_detail['type']) && isset($_detail['unit_id']))
		{
			// no problem to continue;
		}
		else
		{
			return false;
		}

		if($user_as_unverify)
		{
			$budget_before = self::budget_unverify(['type' => $_detail['type'], 'unit' => $_detail['unit_id']]);
		}
		else
		{
			$budget_before = self::budget($_detail['user_id'], ['type' => $_detail['type'], 'unit' => $_detail['unit_id']]);
		}

		$budget_before           = floatval($budget_before);

		$budget                  = $budget_before + (floatval($_detail['plus']) - floatval($_detail['minus']));

		$update                  = [];
		$update['dateverify']    = time();
		$update['budget_before'] = $budget_before;
		$update['budget']        = $budget;

		return $update;


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
				ORDER BY transactions.dateverify DESC, transactions.id DESC
				LIMIT 1
			";
		}
		else
		{
			return false;
		}

		$result = \dash\db::get($query, $field, $only_one_value);

		if(!$result)
		{
			return 0;
		}

		return $result;
	}


	public static function budget($_user_id)
	{
		if(!$_user_id || !is_numeric($_user_id))
		{
			return false;
		}

		$query =
		"
			SELECT budget
			FROM transactions
			WHERE
				transactions.user_id = $_user_id AND
				transactions.verify  = 1
			ORDER BY transactions.dateverify DESC, transactions.id DESC
			LIMIT 1
		";
		return floatval(\dash\db::get($query, 'budget', true));
	}


	/**
	 * get the budget of users
	 *
	 * @param      <type>  $_user_id  The user identifier
	 */
	public static function budget_old($_user_id, $_options = [])
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
					transactions.user_id = $_user_id AND
					transactions.verify  = 1 AND
					transactions.id IN
					(
						SELECT
							transactions.id
						FROM
							transactions
						WHERE
							transactions.user_id = $_user_id AND
							transactions.verify  = 1 AND
							transactions.dateverify =
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
						GROUP BY
							transactions.unit_id
					)
				-- get all budget in all units of users
			";
			$all_unit =  \dash\db::get($all_unit, ['unit', 'budget']);
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
				ORDER BY transactions.dateverify DESC, transactions.id DESC
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
				ORDER BY transactions.dateverify DESC, transactions.id DESC
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
				ORDER BY transactions.dateverify DESC, transactions.id DESC
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
				ORDER BY transactions.dateverify DESC, transactions.id DESC
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
				ORDER BY transactions.dateverify DESC, transactions.id DESC
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
				ORDER BY transactions.dateverify DESC, transactions.id DESC
				LIMIT 1
			)
			";

		}
		$result = \dash\db::get($query, $field, $only_one_value);
		if(!$result)
		{
			return 0;
		}
		return $result;
	}
}
?>
