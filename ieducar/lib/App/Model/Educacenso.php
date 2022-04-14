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
        $etapas = [];

        switch ($etapaEnsino) {
            case '3':
                $etapas = [1, 2];
                break;

            case '22':
            case '23':
                $etapas = [14, 15, 16, 17, 18, 19, 20, 21, 41];
                break;

            case '56':
                $etapas = [1, 2, 14, 15, 16, 17, 18, 19, 20, 21, 41];
                break;

            case '64':
                $etapas = [30, 40];
                break;

            case '72':
                $etapas = [69, 70];
                break;
        }

        return $etapas;
    }
}
