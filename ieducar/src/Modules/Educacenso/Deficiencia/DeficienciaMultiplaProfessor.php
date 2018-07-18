<?php


namespace iEducar\Modules\Educacenso\Deficiencia;


class DeficienciaMultiplaProfessor implements CombinacaoDeficienciaMultipla
{
    public function getCombinacoes()
    {
        return [
            ['19','24'],
            ['19','25'],
            ['20','24'],
            ['20','25'],
            ['21','24'],
            ['21','25'],
            ['22','24'],
            ['22','25'],
            ['23','24'],
            ['23','25'],
            ['19','22'],
            ['20','21'],
            ['20','22'],
            ['24','25'],
        ];
    }
}