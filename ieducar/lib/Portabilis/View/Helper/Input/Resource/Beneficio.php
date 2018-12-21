<?php

require_once 'lib/Portabilis/View/Helper/Input/CoreSelect.php';

class Portabilis_View_Helper_Input_Resource_Beneficio extends Portabilis_View_Helper_Input_CoreSelect
{
    protected function inputOptions($options)
    {
        $resources = $options['resources'];

        if (empty($resources)) {
            $resources = new clsPmieducarAlunoBeneficio();
            $resources = $resources->lista(null, null, null, null, null, null, null, null, null, 1);
            $resources = Portabilis_Array_Utils::setAsIdValue($resources, 'cod_aluno_beneficio', 'nm_beneficio');
        }

        return $this->insertOption(null, Portabilis_String_Utils::toLatin1('Benefício'), $resources);
    }

    public function beneficio($options = [])
    {
        parent::select($options);
    }
}
