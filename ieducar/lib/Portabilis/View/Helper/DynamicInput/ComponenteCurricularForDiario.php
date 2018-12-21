<?php

require_once 'lib/Portabilis/View/Helper/DynamicInput/Core.php';
require_once 'lib/Portabilis/View/Helper/DynamicInput/ComponenteCurricular.php';

class Portabilis_View_Helper_DynamicInput_ComponenteCurricularForDiario extends Portabilis_View_Helper_DynamicInput_ComponenteCurricular
{
    public function componenteCurricularForDiario($options = [])
    {
        return parent::componenteCurricular($options);
    }
}
