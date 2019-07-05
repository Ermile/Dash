<?php
namespace dash\engine;
/**
 * Create static files
 */
class static_files
{
	public static function human()
	{
		$contributors = "";
		// general detail
		$contributors .= '/** '. T_("In The name of Allah"). ' **/' . "\n\n\n";

		$contributors .= T_("Proudly made in IRAN, powered by Dash."). "\n";
		$contributors .= "https://github.com/Ermile/dash". "\n\n";

		$contributors .= T_("Ermile is built by a creative team of engineers, designers, researchers and others in many different sites across the globe. It is updated continuously, and built with more tools and technologies than we can shake a stick at. If you'd like to help us out, see ermile.com/careers."). "\n";


		// teams member
		$contributors .= "\n\n". "/* ". T_('TEAM'). "*/";

		// Javad Evazzadeh Kakroudi
		$contributors .= "\n\t". T_("Javad Evazzadeh Kakroudi");
		$contributors .= "\n\t". T_('Website'). ": https://evazzadeh.com";
		$contributors .= "\n\t". T_('Contact'). ": J.Evazzadeh [at] live.com";
		$contributors .= "\n\t". T_('Twitter'). ": @evazzadeh";
		$contributors .= "\n\t". T_('Github'). ": @evazzadeh";
		$contributors .= "\n\t". T_('Location'). ": Iran";
		$contributors .= "\n";

		// Reza Mohiti
		$contributors .= "\n\t". T_("Reza Mohiti");
		$contributors .= "\n\t". T_('Website'). ": http://rezamohiti.ir";
		$contributors .= "\n\t". T_('Contact'). ": rm.biqarar [at] gmail.com";
		$contributors .= "\n\t". T_('Twitter'). ": @rmbiqarar";
		$contributors .= "\n\t". T_('Location'). ": Qom, Iran";


		// special thanks to
		$contributors .= "\n\n\n". "/* ". T_('THANKS'). "*/";

		// Mohammad Hasan Salehi HajiAbadi
		$contributors .= "\n\t". T_("Mohammad Hasan Salehi HajiAbadi");
		$contributors .= "\n\t". T_('Contact'). ": itb.Baravak [at] gmail.com";
		$contributors .= "\n\t". T_('Twitter'). ": @baravak";
		$contributors .= "\n\t". T_('Location'). ": Qom, Iran";
		$contributors .= "\n";

		// Saman Soltani
		$contributors .= "\n\t". T_("Saman Soltani");
		$contributors .= "\n\t". T_('Contact'). ': '. "sam.soltani [at] gmail.com";
		$contributors .= "\n\t". T_('Location'). ": Germany";


		// site
		$contributors .= "\n\n\n". "/* ". T_('SITE'). "*/";
		$contributors .= "\n\t". "Last update: 05/07/2019";
		$contributors .= "\n\t". "Version: 2.0.0";
		$contributors .= "\n\t". "Language: Farsi / English";
		$contributors .= "\n\t". "Doctype: HTML5";
		$contributors .= "\n\t". "IDE: Sublime!";


		// Ermile
		$contributors .= "\n\n\n";
		$contributors .= '─██████████████─████████████████───██████──────────██████─██████████─██████─────────██████████████─'. "\n";
		$contributors .= '─██░░░░░░░░░░██─██░░░░░░░░░░░░██───██░░██████████████░░██─██░░░░░░██─██░░██─────────██░░░░░░░░░░██─'. "\n";
		$contributors .= '─██░░██████████─██░░████████░░██───██░░░░░░░░░░░░░░░░░░██─████░░████─██░░██─────────██░░██████████─'. "\n";
		$contributors .= '─██░░██─────────██░░██────██░░██───██░░██████░░██████░░██───██░░██───██░░██─────────██░░██─────────'. "\n";
		$contributors .= '─██░░██████████─██░░████████░░██───██░░██──██░░██──██░░██───██░░██───██░░██─────────██░░██████████─'. "\n";
		$contributors .= '─██░░░░░░░░░░██─██░░░░░░░░░░░░██───██░░██──██░░██──██░░██───██░░██───██░░██─────────██░░░░░░░░░░██─'. "\n";
		$contributors .= '─██░░██████████─██░░██████░░████───██░░██──██████──██░░██───██░░██───██░░██─────────██░░██████████─'. "\n";
		$contributors .= '─██░░██─────────██░░██──██░░██─────██░░██──────────██░░██───██░░██───██░░██─────────██░░██─────────'. "\n";
		$contributors .= '─██░░██████████─██░░██──██░░██████─██░░██──────────██░░██─████░░████─██░░██████████─██░░██████████─'. "\n";
		$contributors .= '─██░░░░░░░░░░██─██░░██──██░░░░░░██─██░░██──────────██░░██─██░░░░░░██─██░░░░░░░░░░██─██░░░░░░░░░░██─'. "\n";

		// try to show it
		\dash\code::jsonBoom($contributors, true, 'text');
	}


	public static function robots()
	{
		$robots = "";
		// allow all user agents
		$robots .= "User-Agent: *". "\n";

		// disallow
		$robots .= "Disallow: /cgi-bin/". "\n";
		$robots .= "Disallow: /static/". "\n";
		$robots .= "Disallow: /enter/". "\n";
		$robots .= "Disallow: /account/". "\n";
		$robots .= "Disallow: /a/". "\n";
		$robots .= "Disallow: /cp/". "\n";
		$robots .= "Disallow: /su/". "\n";
		$robots .= "Disallow: /crm/". "\n";
		$robots .= "Disallow: /cms/". "\n";
		$robots .= "Disallow: /tmp/". "\n";
		$robots .= "Disallow: /*.txt$". "\n\n";

		// allow
		$robots .= "Sitemap: /sitemap.xml". "\n";


		\dash\code::jsonBoom($robots, true, 'text');
	}

}
?>
