<?php

class App_Model_Educacenso
{
    public static function etapas_multisseriadas()
    {
        return [3, 22, 23, 56, 64, 72];
    }

    public static function etapasEnsinoUnificadas()
    {
        return [3];
    }

    public static function etapasDaTurma($etapaEnsino)
    {
        return match ((string)$etapaEnsino) {
            '3' => [1, 2],
            '22', '23' => [14, 15, 16, 17, 18, 19, 20, 21, 41],
            '56' => [1, 2, 14, 15, 16, 17, 18, 19, 20, 21, 41],
            '64' => [30, 40],
            '72' => [69, 70],
            default => [],
        };
    }
}
