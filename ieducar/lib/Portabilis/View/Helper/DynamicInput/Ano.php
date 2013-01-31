<?php

require_once 'lib/Portabilis/View/Helper/Input/Ano.php';


/*

  Helper DEPRECADO

  #TODO mover referencias de $this->inputsHelper()->dynamic('ano');
  para $this->inputsHelper()->input('ano');

*/

if (strpos($_SERVER['HTTP_HOST'], 'local') > -1)
  echo "Helper DynamicInput_Ano DEPRECADO";

class Portabilis_View_Helper_DynamicInput_Ano extends Portabilis_View_Helper_Input_Ano {
}
