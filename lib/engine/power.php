<?php
namespace lib\engine;
/**
 * dash main configure
 */
class power
{

	public static function on()
	{
		\lib\engine\prepare::requirements();

		// block baby to not allow to harm yourself :/
		\lib\engine\baby::block();

		// detect url and start work with them as first lib used by another one
		\lib\url::initialize();

		// detect language and if need set the new language
		\lib\language::detect_language();

		\lib\engine\prepare::basics();


		// // check if isset remember me and login by this
		\lib\user::check_remeber_login();

		// @check
		\lib\user::user_country_redirect();


		// LAUNCH !
		\lib\engine\mvc::fire();
	}
}
?>
