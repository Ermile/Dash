<?php
namespace addons\content_cp\tools\sitemap;

class view extends \lib\mvc\view
{
	public function config()
	{
		$this->data->page['title']   = T_('Sitemap');

		if(\lib\utility::get('run') === 'yes')
		{
			$this->data->sitemapData = $this->model()->generate_sitemap();
		}
	}
}
?>