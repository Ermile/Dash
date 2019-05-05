<?php
namespace content_cms\dayevent;


class view
{
	public static function config()
	{
		\dash\data::page_title(T_('Day event chart!'));
		\dash\data::page_desc(T_('Check change of this data in date'));
		\dash\data::page_pictogram('chart-line');

		\dash\data::badge_text(T_('Back to dashboard'));
		\dash\data::badge_link(\dash\url::this());


		$chart = \dash\utility\dayevent::chart();
		\dash\data::cahrtDetail($chart);

	}
}
?>
