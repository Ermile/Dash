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

	public static function make()
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
}
?>