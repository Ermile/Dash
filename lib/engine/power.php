<?php
namespace dash\engine;
/**
 * dash main configure
 */
class power
{

	public static function on()
	{
		\dash\engine\prepare::requirements();

		// detect url and start work with them as first lib used by another one
		\dash\url::initialize();

		// block baby to not allow to harm yourself :/
		\dash\engine\baby::block();

		// detect language and if need set the new language
		\dash\language::detect_language();

		\dash\engine\prepare::basics();


		// // check if isset remember me and login by this
		\dash\user::check_remeber_login();


		// LAUNCH !
		\dash\engine\mvc::fire();
	}
}
?>
