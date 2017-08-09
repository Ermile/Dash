<?php
 
/**
 * Execute a PHP template file and return the result as a string.
 */
function apply_template($tpl_file, $vars = array(), $include_globals = true)
{
  extract($vars);
 
  if ($include_globals) extract($GLOBALS, EXTR_SKIP);
 
  ob_start();
 
  require($tpl_file);
 
  $applied_template = ob_get_contents();
  ob_end_clean();
 
  return $applied_template;
}
 
?>