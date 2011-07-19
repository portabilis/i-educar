<?php

#error_reporting(E_ALL);
#ini_set("display_errors", 1);

$paths_to_include = array();
$paths_to_include[] = dirname(__FILE__) . '/libs'; #/intranet/include/portabilis/libs
$paths_to_include[] = dirname(dirname(dirname(__FILE__))); #/intranet

foreach ($paths_to_include as $p)
  set_include_path(get_include_path() . PATH_SEPARATOR . $p);

?>
