<?php
namespace content_crm\duplicatequery;


class view
{
	public static function config()
	{
		if(!\dash\permission::supervisor())
		{
			\dash\header::status(403);
		}

		switch (\dash\request::get('type'))
		{
			case 'numberusername':
				$query          = "SELECT * FROM users WHERE users.username IS NOT NULL";
				$username       = \dash\db::get($query);
				$similar_mobile = [];
				$ids            = [];

				foreach ($username as $key => $value)
				{
					if(is_numeric($value['username']))
					{
						$similar_mobile[] = $value;
						$ids[] = $value['id'];
					}
				}


				\dash\data::dataTable($similar_mobile);

				if(\dash\request::get('run') === 'run' && !empty($ids))
				{
					$ids = implode(',', $ids);
					$query = "UPDATE users SET users.username = NULL WHERE users.id IN ($ids)";
					\dash\db::query($query);
				}
				break;


			case 'emailusername':
				$query         = "SELECT * FROM `users` WHERE `username` LIKE '%@%' ";
				$emailusername = \dash\db::get($query);

				\dash\data::dataTable($emailusername);

				if(\dash\request::get('run') === 'run' && !empty($emailusername))
				{
					$ids = implode(',', array_column($emailusername, 'id'));
					$query = "UPDATE users SET users.username = NULL WHERE users.id IN ($ids)";
					\dash\db::query($query);
				}
				break;

			case 'persianusername':
				$query         = "SELECT * FROM `users` WHERE `username` IS NOT NULL ";
				$persianusername = \dash\db::get($query);

				$similar_mobile = [];
				$ids            = [];

				foreach ($persianusername as $key => $value)
				{
					if(preg_match("/[ا-ی]/", $value['username']))
					{
						$similar_mobile[] = $value;
						$ids[] = $value['id'];
					}
				}

				\dash\data::dataTable($similar_mobile);

				if(\dash\request::get('run') === 'run' && !empty($similar_mobile))
				{
					$ids = implode(',', array_column($similar_mobile, 'id'));
					$query = "UPDATE users SET users.username = NULL WHERE users.id IN ($ids)";
					\dash\db::query($query);
				}
				break;


			case 'awaitingnorecord':
				$query         =
				"
				SELECT
				(SELECT COUNT(*) FROM useracademies WHERE useracademies.user_id = users.id ) AS useracademies_count,
				(SELECT COUNT(*) FROM academies WHERE academies.creator = users.id ) AS academies_count,
				(SELECT COUNT(*) FROM users as mU WHERE mU.mobile = users.mobile ) AS `mobile_count`,
				users.*

				FROM users

				WHERE users.status = 'awaiting' AND users.datemodified IS NULL

				And 'useracademies_count' = 0
				And 'academies_count' = 0

				And (SELECT COUNT(*) FROM users as mU WHERE mU.mobile = users.mobile ) > 1


				ORDER BY `mobile_count` DESC
				";
				$awaitingnorecord = \dash\db::get($query);



				\dash\data::dataTable($awaitingnorecord);

				if(\dash\request::get('run') === 'run' && !empty($awaitingnorecord))
				{
					foreach ($awaitingnorecord as $key => $value)
					{
						$removed = \dash\app\user::delete_user($value['id']);
					}
				}
				break;


			default:
				# code...
				break;
		}

	}
}
?>