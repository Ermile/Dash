<?php
namespace dash\app;


class user_auth
{
	private static function generate_auth($_user_id = null)
	{
		$string = '';
		$string .= 'Ermile';
		$string .= (string) time();
		$string .= 'Dash';
		$string .= (string) rand();
		$string .= 'Api';
		$string .= (string) rand();
		$string .= 'Token';
		$string .= '_';
		$string .= $_user_id;
		$string .= '_';
		$string .= (string) microtime();
		$string = md5($string);
		return $string;
	}

	public static function make($_args = [])
	{
		$auth                  = self::generate_auth();
		$date_now              = date("Y-m-d H:i:s");
		$insert                = [];
		$insert['auth']        = $auth;
		$insert['user_id']     = null;
		$insert['status']      = 'enable';
		$insert['gateway']     = isset($_args['gateway']) ? $_args['gateway'] : null;
		$insert['type']        = 'guest';
		$insert['datecreated'] = $date_now;

		$insert = \dash\db\user_auth::insert($insert);
		if($insert)
		{
			$result = [];
			$result['auth'] = $auth;
			$result['date'] = $date_now;
			return $result;
		}
		return false;

	}

	public static function make_user_auth($_user_id, $_gateway = null)
	{
		$check =
		[
			'user_id' => $_user_id,
			'type'    => 'member',
			'status'  => 'enable',
			'gateway' => $_gateway,
			'limit'   => 1,
		];

		$check = \dash\db\user_auth::get($check);
		if(isset($check['auth']))
		{
			return $check['auth'];
		}
		else
		{
			$auth = self::generate_auth($_user_id);

			$insert =
			[
				'user_id'     => $_user_id,
				'type'        => 'member',
				'status'      => 'enable',
				'gateway'     => $_gateway,
				'datecreated' => date("Y-m-d H:i:s"),
				'auth'        => $auth,
			];

			$insert = \dash\db\user_auth::insert($insert);

			if($insert)
			{
				return $auth;
			}

			return false;
		}
	}
}
?>