<?php
namespace dash\utility;


class hive
{
	public static function set($_redirect = false)
	{
		// if the user is login not check and not set
		if(\dash\user::id())
		{
			return true;
		}

		$request = \dash\request::is();
		$request = mb_strtolower($request);
		if($request === 'get')
		{
			self::make();
		}
		elseif($request === 'post')
		{
			if(!self::check())
			{
				if($_redirect)
				{
					\dash\redirect::pwd();
				}
				else
				{
					\dash\header::status(400, T_("Reload page to continue!"));
				}
			}
		}
		else
		{
			\dash\header::status(400, T_("You wander in a labyrinth!"));
		}
	}

	private static function make()
	{
		$hive = [];
		$element = [1, 2, 3];
		shuffle($element);

		$hive['check'. $element[0]] = '';
		$hive['check'. $element[1]] = md5((string) time(). (string) rand(). (string) rand());
		$hive['check'. $element[2]] = \dash\url::this();

		\dash\data::hive($hive);
		\dash\session::set('hive_'. \dash\url::this(), $hive);
	}


	private static function check()
	{
		$hive = \dash\session::get('hive_'. \dash\url::this());
		$hiveCheck1 = \dash\request::post('hiveCheck1');
		$hiveCheck2 = \dash\request::post('hiveCheck2');
		$hiveCheck3 = \dash\request::post('hiveCheck3');

		if(!isset($hive['check1']) || (isset($hive['check1']) && $hiveCheck1 != $hive['check1']))
		{
			return false;
		}

		if(!isset($hive['check2']) || (isset($hive['check2']) && $hiveCheck2 != $hive['check2']))
		{
			return false;
		}

		if(!isset($hive['check3']) || (isset($hive['check3']) && $hiveCheck3 != $hive['check3']))
		{
			return false;
		}

		return true;
	}

}