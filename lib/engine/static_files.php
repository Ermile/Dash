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
		$contributors .= "/** In The name of Allah **/" . "\n\n\n";

		$contributors .= "Proudly made in IRAN, powered by Dash.". "\n";
		$contributors .= "https://github.com/Ermile/dash". "\n\n";

		$contributors .= "Ermile is built by a creative team of engineers, designers, researchers and others in many different sites across the globe. It is updated continuously, and built with more tools and technologies than we can shake a stick at. If you'd like to help us out, see ermile.com/careers.\n";


		// teams member
		$contributors .= "\n\n". "/* TEAM */";

		// Javad Evazzadeh Kakroudi
		$contributors .= "\n\t". "Javad Evazzadeh Kakroudi";
		$contributors .= "\n\t". "Site: https://evazzadeh.com";
		$contributors .= "\n\t". "Contact: J.Evazzadeh [at] live.com";
		$contributors .= "\n\t". "Twitter: @evazzadeh";
		$contributors .= "\n\t". "Github: @evazzadeh";
		$contributors .= "\n\t". "Location: Iran";
		$contributors .= "\n";

		// Reza Mohiti
		$contributors .= "\n\t". "Reza Mohiti";
		$contributors .= "\n\t". "Site: http://rezamohiti.ir";
		$contributors .= "\n\t". "Contact: rm.biqarar [at] gmail.com";
		$contributors .= "\n\t". "Twitter: @rmbiqarar";
		$contributors .= "\n\t". "Location: Qom, Iran";


		// special thanks to
		$contributors .= "\n\n\n". "/* THANKS */";

		// Mohammad Hasan Salehi HajiAbadi
		$contributors .= "\n\t". "Mohammad Hasan Salehi HajiAbadi";
		$contributors .= "\n\t". "Contact: itb.Baravak [at] gmail.com";
		$contributors .= "\n\t". "Twitter: @baravak";
		$contributors .= "\n\t". "Location: Qom, Iran";
		$contributors .= "\n";

		// Samam Soltani
		$contributors .= "\n\t". "Samam Soltani";
		$contributors .= "\n\t". "Contact: sam.soltani [at] gmail.com";
		$contributors .= "\n\t". "Location: Germany";


		// site
		$contributors .= "\n\n\n". "/* SITE */";
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
