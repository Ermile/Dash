<?php
namespace lib\utility\validate;

return function()
{
	if(!preg_match("/^\d+$/", $this->value))
		return false;
	else
		return true;
}
?>