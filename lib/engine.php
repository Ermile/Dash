<?php
namespace lib;
/**
 * dash main configure
 */
class engine
{

	public static function launch()
	{
		// check debug and set php ini
		\lib\engine\dev::set_php_ini();

		// block baby to not allow to harm yourself :/
		\lib\engine\baby::block();

		// check min requirement to run dash core!
		\lib\engine\init::minimum_requirement();

		// detect url and start work with them as first lib used by another one
		\lib\url::initialize();

		// detect language and if need set the new language
		\lib\language::detect_language();

		// check comming soon page
		\lib\engine\init::coming_soon();

		// check need redirect for lang or www or https or main domain
		\lib\engine\init::appropriate_url();

		// start session
		\lib\session::start();

		// // check if isset remember me and login by this
		\lib\user::check_remeber_login();

		// @check
		\lib\user::user_country_redirect();

		// LAUNCH !
		\lib\engine\main::start();

	}
}
?>
