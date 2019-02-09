<?php
namespace content_enter\signup;

class view
{

	public static function config()
	{
		\dash\data::page_special(true);
		\dash\data::page_title(T_('Signup in :name' , ['name' => \dash\data::site_title()]));
		\dash\data::page_desc(\dash\data::page_title());

		// set el value to use in display
		\dash\data::el_username(\dash\option::config('enter', 'singup_username'));


		$termLink = '<a href="'. \dash\url::kingdom(). '/terms" target="_blank">'. T_('Terms of Service') .'</a>';
		$privacyLink = '<a href="'. \dash\url::kingdom(). '/privacy" target="_blank">'. T_('Privacy Policy') .'</a>';

		\dash\data::termOfService(T_("By clicking Sign Up, you are indicating that you have read the :privacy and agree to the :terms.", ['privacy' => $privacyLink, 'terms' => $termLink]));
	}
}
?>