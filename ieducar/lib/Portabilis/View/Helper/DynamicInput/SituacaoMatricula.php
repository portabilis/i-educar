<?php

require_once 'lib/Portabilis/View/Helper/DynamicInput/CoreSelect.php';

class Portabilis_View_Helper_DynamicInput_SituacaoMatricula extends Portabilis_View_Helper_DynamicInput_CoreSelect
{
    protected function inputOptions($options)
    {
        $resources = $options['resources'];

        $resources = [
            1 => 'Aprovado',
            2 => 'Reprovado',
            3 => 'Cursando',
            4 => 'Transferido',
            5 => 'Reclassificado',
            6 => 'Abandono',
            9 => 'Exceto Transferidos/Abandono',
            10 => 'Todas',
            12 => 'Aprovado com dependência',
            13 => 'Aprovado pelo conselho',
            14 => 'Reprovado por faltas',
            15 => 'Falecido'
        ];

        return $this->insertOption(10, 'Todas', $resources);
    }

    protected function defaultOptions()
    {
        return ['options' => ['label' => 'Situação']];
    }

    public function situacaoMatricula($options = [])
    {
        parent::select($options);
    }
}
