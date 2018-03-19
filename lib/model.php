<?php
namespace lib;

class model
{

	/**
	 * construct
	 *
	 * @param      boolean  $_startObject  The object
	 */
	public function __construct($_startObject = false)
	{
		if(method_exists($this, '_construct'))
		{
			$this->_construct();
		}
	}
}
?>