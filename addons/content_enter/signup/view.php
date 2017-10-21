<?php
namespace addons\content_enter\signup;

class view extends \addons\content_enter\main\view
{

	/**
	 * view enter
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function view_signup($_args)
	{
		$this->data->page['special'] = true;
		$this->data->page['title']   = T_('Signup in Tejarak');
		$this->data->page['desc']    = $this->data->page['title'];
	}
}
?>