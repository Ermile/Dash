<?php
namespace addons\content_su\tools\sitemap;

class view extends \lib\view
{
	public function config()
	{
		$this->data->bodyclass        = 'siftal';


		$this->data->page['title']   = T_('Sitemap');

		if(\lib\request::get('run') === 'yes')
		{
			$this->data->sitemapData = $this->model()->generate_sitemap();
		}
	}
}
?>