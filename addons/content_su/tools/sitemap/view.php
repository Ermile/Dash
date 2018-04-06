<?php
namespace addons\content_su\tools\sitemap;

class view extends \dash\view
{
	public function config()
	{
		$this->data->bodyclass        = 'siftal';


		$this->data->page['title']   = T_('Sitemap');

		if(\dash\request::get('run') === 'yes')
		{
			$this->data->sitemapData = $this->model()->generate_sitemap();
		}
	}
}
?>