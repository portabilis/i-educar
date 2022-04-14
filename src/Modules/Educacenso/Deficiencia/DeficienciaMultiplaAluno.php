<?php

namespace iEducar\Modules\Educacenso\Deficiencia;

class DeficienciaMultiplaAluno implements CombinacaoDeficienciaMultipla
{
    public function getCombinacoes()
    {
        return [
            ['17', '22'],
            ['17', '23'],
            ['18', '22'],
            ['18', '23'],
            ['19', '22'],
            ['19', '23'],
            ['20', '22'],
            ['20', '23'],
            ['21', '22'],
            ['21', '23'],
            ['17', '20'],
            ['18', '19'],
            ['18', '20'],
            ['22', '23'],
        ];
    }
}
