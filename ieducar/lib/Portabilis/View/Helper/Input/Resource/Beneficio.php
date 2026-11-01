<?php

class Portabilis_View_Helper_Input_Resource_Beneficio extends Portabilis_View_Helper_Input_CoreSelect
{
    protected function inputOptions($options)
    {
        $resources = $options['resources'];

        if (empty($resources)) {
            // A classe clsPmieducarAlunoBeneficio não existe mais: a criação abaixo
            // derruba a tela, e o resto do bloco não chega a rodar.
            $resources = new clsPmieducarAlunoBeneficio;
            // @codeCoverageIgnoreStart
            $resources = $resources->lista(null, null, null, null, null, null, null, null, null, 1);
            $resources = Portabilis_Array_Utils::setAsIdValue($resources, 'cod_aluno_beneficio', 'nm_beneficio');
            // @codeCoverageIgnoreEnd
        }

        return $this->insertOption(null, 'Benefício', $resources);
    }

    public function beneficio($options = [])
    {
        parent::select($options);
    }
}
